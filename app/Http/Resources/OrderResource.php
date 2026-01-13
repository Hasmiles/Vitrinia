<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $customer = $this->whenLoaded('customer');
        $product = $this->whenLoaded('products');
        return [
            'id' => $this->id,
            'seler_id' => $this->seller_id,
            'customer_name' => $customer ? $customer->fullname : '',
            'customer_phone' => $customer ? $customer->phone : '',
            'customer_address' => $customer ? $customer->address : '',
            'customer_note' => $customer ? $customer->note : '',
            'total_amount' => $product->sum('price'),
            'status' => $this->status,
            'payment_method' => $this->type == 1 ? 'iban' : 'cash',
            'short_code' => $this->short_code,
            // 'product_data' => OrderProductResource::collection($product),
            'product_data' => new OrderProductResource($product->first()),
            'created_at' => $this->created_at
        ];
    }
}
