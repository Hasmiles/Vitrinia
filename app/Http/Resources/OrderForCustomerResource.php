<?php

namespace App\Http\Resources;

use App\Models\SubOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderForCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = $this->whenLoaded('customer');
        $product = $this->whenLoaded('products');
        $seller = $this->whenLoaded('seller');
        return [
            'id' => $this->id,
            'seller_id' => $this->seller_id,
            // 'customer_name' => $customer ? $customer->fullname : '',
            // 'customer_phone' => $customer ? $customer->phone : '',
            // 'customer_city' => $customer ? $customer->city : '',
            // 'customer_city' => $customer ? $customer->town : '',
            // 'customer_address' => $customer ? $customer->address : '',
            // 'customer_note' => $customer ? $customer->note : '',
            'total_amount' => $product->sum('price'),
            'status' => $this->status,
            'payment_method' => $this->type == 1 ? 'iban' : 'cash',
            'short_code' => $this->short_code,
            'product_data' => OrderProductResource::collection($product),
            'customer_data' => new CustomerResource($customer)
            // 'seller' => new SellerResource($seller)
        ];
    }
}
