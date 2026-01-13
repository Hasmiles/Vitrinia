<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductDeleteRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Option;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\SubOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('subOptions')->get();
        return response()->json([
            'products' => ProductResource::collection($products)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'price' => ['required', 'decimal:0,2']
        // ]);
        DB::beginTransaction();
        try {
            DB::commit();
            /** @var App/Model/User $user */
            $user = Auth::user();
            $customFileName = '';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('products', 'public');
                $customFileName = basename($path);
            }
            $product = new Product(
                [
                    'seller_id' => $user->seller->id,
                    'name' => $request->name,
                    'image' => $customFileName,
                    'price' => $request->price,
                    'stock' => $request->stock
                ]
            );
            $product->save();
            $values = $request->values;
            if ($values) {
                $sub_option = SubOption::whereIn('value', $values)->pluck('id');
                $product->subOptions()->attach($sub_option);
            }
            $product->load('subOptions.mainOption');
            return response()->json([
                'success' => true,
                'message' => 'Ürün eklendi',
                'data' => new ProductResource($product)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ürün eklenirken hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('subOptions');
        return response()->json(['product' => new ProductResource($product)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        Log::info($request->all());
        DB::beginTransaction();
        try {
            $product->name = $request->name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $customFileName = $product->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('products', 'public');
                $customFileName = basename($path);
            }
            $product->image = $customFileName;
            $product->save();
            $values = $request->values;
            if ($values) {
                $sub_option = SubOption::whereIn('value', $values)->pluck('id');
                $product->subOptions()->attach($sub_option);
            }else{
                $product->subOptions()->detach();
            }
            $product->load('subOptions');
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Ürün güncellendi',
                'product' => new ProductResource($product)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => true,
                'message' => 'Ürün güncellenirken hata oluştu.',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductDeleteRequest $request)
    {
        
        DB::beginTransaction();
        try {
            $ids = $request->ids;
            $count = Product::whereIn('id', $ids)->count();
            if ($count !== count($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bazı ürünler bulunamadı, işlem iptal edildi.'
                ], 404);
            }
            Product::destroy($ids);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ürünler silindi'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ürün Silinemedi',
                'error' => $e->getMessage()
            ]);
        }
    }
}
