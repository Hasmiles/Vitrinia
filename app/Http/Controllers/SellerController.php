<?php

namespace App\Http\Controllers;

use App\Http\Resources\LowStockResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\PopularProductResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellerController extends Controller
{
    /**
     * @var \App\Models\User $user
     */
    public function index()
    {
        $user = User::with('seller')->find(Auth::user()->id);
        $profile = [
            'id' => $user->id,
            'shop_name' => $user->seller->shop_name,
            'phone' => $user->seller->phone,
            'iban' => $user->seller->iban,
            'owner_name' => $user->name,
            'image' => $user->seller->logo ? asset('storage/sellers/' . $user->seller->logo) : '',
            'is_completed' => $user->seller->is_completed == 0 ? false : true,
            'verification_code' => null,
            'push_token' => $user->fcm_token,
            'is_verified' => $user->is_verified ? true : false
        ];
        return response()->json($profile, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->name = $request->owner_name ?? '';
        $user->save();
        $seller = Seller::where('user_id', $user->id)->first();
        $seller->shop_name = $request->shop_name ?? '';
        $seller->phone = $request->phone ?? '';
        $seller->iban = $request->iban ?? '';
        $seller->is_completed = 1;
        $customFileName = $seller->logo ?? '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('sellers', 'public');
            $customFileName = basename($path);
        }
        $seller->logo = $customFileName;
        $seller->save();
        $profile = [
            'id' => $user->id,
            'shop_name' => $user->seller->shop_name,
            'phone' => $user->seller->phone,
            'iban' => $user->seller->iban,
            'owner_name' => $user->name,
            'image' => $user->seller->logo ? asset('storage/sellers/' . $user->seller->logo) : '',
            'is_completed' => $user->seller->is_completed == 0 ? false : true,
            'verification_code' => null,
            'push_token' => $user->fcm_token,
            'is_verified' => $user->is_verified
        ];
        return response()->json($profile, 200);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy()
    {
        $user = User::find(Auth::user()->id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Hesap Silindi'
        ], 200);
    }

    public function updatePushToken(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->fcm_token = $request->push_token;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Push Token Güncellendi'
        ], 200);
    }

    public function dashboard(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $user = Auth::user();
        $seller = $user->seller;
        $orders = Order::where('seller_id', $seller->id);
        if ($start_date && $end_date) {
            $orders->whereBetween('created_at', [$start_date, $end_date]);
            $currentStart = Carbon::parse($request->start_date);
            $currentEnd = Carbon::parse($request->end_date);
            $diffInDays = $currentStart->diffInDays($currentEnd);
            $prevStart = $currentStart->copy()->subDays($diffInDays + 1);
            $prevEnd = $currentStart->copy()->subDay();
        } else {
            $currentStart = now()->startOfMonth();  // 1 Ocak
            $currentEnd = now();  // 11 Ocak (Bugün)

            $prevStart = now()->subMonth()->startOfMonth();  // 1 Aralık
            $prevEnd = now()->subMonth();
        }

        $stats = $orders->clone()->selectRaw('
            COUNT(*) as total_orders,
            SUM((SELECT SUM(price) FROM order_products WHERE order_id = orders.id)) as total_revenue,
            SUM(CASE WHEN status = 5 THEN 1 ELSE 0 END) as total_completed,
            SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as total_pending
        ')->first();
        $prevStats = Order::where('seller_id', $seller->id)
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->selectRaw('
            SUM((SELECT SUM(price) FROM order_products WHERE order_id = orders.id)) as total_revenue
        ')
            ->first();
        $previousRevenue = $prevStats->total_revenue ?? 0;
        $growthRate = 0;

        if ($previousRevenue > 0) {
            $growthRate = (($stats->total_revenue - $previousRevenue) / $previousRevenue) * 100;
        } elseif ($stats->total_revenue > 0) {
            $growthRate = 100;  
        } else {
            $growthRate = 0;  
        }
        $summary = [
            'totalRevenue' => $stats->total_revenue ?? 0,
            'totalOrders' => $stats->total_orders,
            'totalCompleted' => $stats->total_completed,
            'growthRate' => $growthRate,  // En son
            'totalPending' => $stats->total_pending,
        ];
        $my_products = Product::where('seller_id', $seller->id);
        $topSellingProducts = PopularProductResource::collection($my_products->clone()->withSum('orderProduct', 'price')->withCount('orderProduct')->orderBy('order_product_count', 'desc')->limit(3)->get());

        $lowStockAlerts = LowStockResource::collection($my_products->where('stock', '<', 5)->get());
        $paymentMethods = PaymentMethodResource::collection($orders
            ->clone()
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get());

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $dailyDB = $orders
            ->clone()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(created_at) as date, SUM((SELECT SUM(price) FROM order_products WHERE order_id = orders.id)) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $dailyLabels = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
        $dailyData = [];
        $dailyTotal = 0;

        for ($i = 0; $i < 7; $i++) {
            $dateKey = $startOfWeek->copy()->addDays($i)->format('Y-m-d');

            $val = $dailyDB[$dateKey] ?? 0;
            $dailyData[] = $val;
            $dailyTotal += $val;
        }

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $weeklyDB = $orders
            ->clone()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('WEEK(created_at, 1) as week_num, SUM((SELECT SUM(price) FROM order_products WHERE order_id = orders.id)) as total')
            ->groupBy('week_num')
            ->pluck('total', 'week_num');

        $weeklyLabels = [];
        $weeklyData = [];
        $weeklyTotal = 0;

        $weeksInMonth = $endOfMonth->weekOfYear - $startOfMonth->weekOfYear + 1;
        if ($weeksInMonth < 0)
            $weeksInMonth = 5;

        for ($i = 0; $i < $weeksInMonth; $i++) {
            $currentWeekNum = $startOfMonth->weekOfYear + $i;

            $weeklyLabels[] = ($i + 1) . '. Hafta';

            $val = $weeklyDB[$currentWeekNum] ?? 0;
            $weeklyData[] = $val;
            $weeklyTotal += $val;
        }

        $monthlyLabels = [];
        $monthlyData = [];
        $monthlyTotal = 0;

        $startMonthDate = now()->subMonths(5)->startOfMonth();

        $monthlyDB = $orders
            ->clone()
            ->where('created_at', '>=', $startMonthDate)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, SUM((SELECT SUM(price) FROM order_products WHERE order_id = orders.id)) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        for ($i = 0; $i < 6; $i++) {
            $date = $startMonthDate->copy()->addMonths($i);
            $key = $date->format('Y-m');

            $monthlyLabels[] = $date->locale('tr')->isoFormat('MMM');  // Oca, Şub...

            $val = $monthlyDB[$key] ?? 0;
            $monthlyData[] = $val;
            $monthlyTotal += $val;
        }

        return response()->json([
            'summary' => $summary,
            'charts' => [
                'daily' => [
                    'labels' => $dailyLabels,
                    'datasets' => [['data' => $dailyData]],
                    'total' => $dailyTotal,
                ],
                'weekly' => [
                    'labels' => $weeklyLabels,
                    'datasets' => [['data' => $weeklyData]],
                    'total' => $weeklyTotal,
                ],
                'monthly' => [
                    'labels' => $monthlyLabels,
                    'datasets' => [['data' => $monthlyData]],
                    'total' => $monthlyTotal,
                ],
            ],
            'topSellingProducts' => $topSellingProducts,
            'paymentMethods' => $paymentMethods,
            'lowStockAlerts' => $lowStockAlerts
        ]);
    }
}
