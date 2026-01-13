<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderForCustomerResource;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderProduct;
use App\Models\OrderProductOption;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SubOption;
use App\Models\User;
use App\Services\FCMService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;

class OrderController extends Controller
{
    /**
     * @var App/Model/User $user
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('seller_id', $user->seller->id)
            ->orderBy('created_at', 'desc')
            ->with(['products.product.subOptions', 'customer', 'products.option'])
            ->get();
        return response()->json(['orders' => OrderResource::collection($orders)], 200);
    }

    public function store(OrderRequest $request)
    {
        $user = Auth::user();
        $productId = $request->input('product_id');
        $type = $request->payment_method == 'iban' ? 1 : 2;
        DB::beginTransaction();
        try {
            $product = Product::find($productId);
            if (!$product) {
                return response()->json(['hata' => 'Ürün yok'], 404);
            }
            if (is_null($product->stock) && $product->stock < 0) {
                return response()->json(['hata' => 'Stokta kalmadı'], 404);
            }

            $status = 1;
            $stat_ids = [1];
            if ($type == 2) {
                $status = 5;
                $stat_ids = [5];
            }

            $kisaKod = Str::random(5);  // Güvenlik zaafiyetlerini bi araştır.

            $order = Order::create([
                'seller_id' => $user->seller->id,
                'customer_id' => 0,
                'type' => $type,
                'status' => $status,
                'short_code' => $kisaKod
            ]);
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price * 1,
                'active' => 1
            ]);
            // $orderLog = OrderLog::create([
            //     'order_id' => $order->id,
            //     'status' => $status
            // ]);
            $order->logs()->attach($stat_ids);
            $product->decrement('stock');
            $order->load(['products.product.subOptions', 'customer', 'products.option']);
            DB::commit();
            return response()->json([
                'mesaj' => 'Sipariş oluşturuldu!',
                'link' => "https://butikasistanim.vercel.app/s/{$kisaKod}",
                'order' => new OrderResource($order),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sipariş eklenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Order $order) {}

    public function update(CustomerRequest $request, string $short_code)
    {
        Log::info($request->all());
        DB::beginTransaction();
        try {
            $order = Order::where('short_code', $short_code)->first();
            $seller = Seller::find($order->seller_id);
            $user = User::find($seller->user_id);
            if (is_null($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Böyle bir sipariş yok'
                ], 400);
            }
            if ($order->customer_id > 0) {
                $customer = Customer::find($order->customer_id);
                $customer->fullname = $request->fullname;
                $customer->phone = $request->phone;
                $customer->city = $request->city;
                $customer->town = $request->town;
                $customer->address = $request->address;
                $customer->address_2 = $request->address_description ?? '';
                $customer->note = $request->note;
                $customer->save();
            } else {
                $customer = Customer::create([
                    'fullname' => $request->fullname,
                    'phone' => $request->phone,
                    'city' => $request->city ?? '',
                    'town' => $request->town ?? '',
                    'address' => $request->address,
                    'address_2' => $request->address_description ?? '',
                    'note' => $request->note ?? ''
                ]);
            }
            $order->customer_id = $customer->id;
            $order->status = 2;
            $order->logs()->attach(2);
            $values = array_values($request->selected_options);
            if ($values) {
                $sub_options = SubOption::whereIn('value', $values)->pluck('id');
                $order_product_id = OrderProduct::where('order_id', $order->id)->first()->id;
                foreach ($sub_options as $id) {
                    OrderProductOption::create([
                        'order_product_id' => $order_product_id,
                        'option_id' => $id
                    ]);
                }
            }

            $order->save();
            $order->load(['products.product.subOptions', 'customer', 'seller', 'products.option']);
            if ($user && $user->fcm_token) {
                $fcmService = new FCMService();

                $fcmService->sendToUser(
                    $user->fcm_token,
                    'Siparişiniz Alındı! 🚀',
                    'Siparişiniz başarıyla oluşturuldu. Hazırlanıyor...',
                    ['siparis_id' => $order->id]
                );
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Sipariş oluşturuldu!',
                'order' => new OrderForCustomerResource($order),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Customer eklenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrderDetailsForCustomer(string $short_code)
    {
        Log::info($short_code);
        $orders = Order::where('short_code', $short_code)
            ->orderBy('created_at', 'desc')
            ->with(['products.product.subOptions', 'customer', 'seller', 'products.option'])
            ->get();
        return response()->json(['order' => OrderForCustomerResource::collection($orders)], 200);
    }

    public function changeSituationForOrder(Request $request, int $order_id)
    {
        $request->validate([
            'status' => 'required|integer',
        ]);
        $order = Order::find($order_id);
        $status = $request->status;
        $order->status = $status;
        $order->save();
        $order->logs()->attach($status);
        $order->load(['products.product.subOptions', 'customer', 'products.option']);
        return response()->json([
            'message' => 'Sipariş durumu değiştirildi!',
            'order' => new OrderResource($order),
        ], 201);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sipariş silindi'
        ], 200);
    }

    public function createLabel(int $id)
    {
        $order = Order::find($id);
        $seller = Seller::find($order->seller_id);
        $customer_info = [
            'name' => 'Müşteri Bilgisi Eklenmedi',
            'address' => 'Adres Yok',
            'phone' => 'Telefon Numarası Yok'
        ];
        if ($order->customer_id > 0){
            $customer = Customer::find($order->customer_id);
            $customer_info['name'] = $customer->fullname;
            $customer_info['address'] = $customer->address;
            $customer_info['phone'] = $customer->phone;
        }
        $data = [
            'gonderici' => $seller->shop_name,
            'alici' => $customer_info['name'],
            'phone' => $customer_info['phone'],
            'adres' => $customer_info['address'],
            'barkod_no' => $order->barcode ?? '2700000000000',  
        ];

        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($data['barkod_no'], $generator::TYPE_CODE_128));

        $pdf = Pdf::loadView('ptt_cikti', compact('data', 'barcode'));

        // PTT Haber Kartı genelde A6 veya özel boyuttur.
        // Özel boyut için: ->setPaper([0, 0, 283.46, 425.20], 'landscape'); // Nokta (pt) cinsinden
        $pdf->setPaper('a6', 'landscape');

        return $pdf->download('ptt-kart-' . $data['barkod_no'] . '.pdf');
    }
}
