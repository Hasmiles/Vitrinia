<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopularProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'salesCount' => $this->order_product_count,
            'revenue' => $this->order_product_sum_price,
            'image_url' => $this->image ? asset('storage/products/' . $this->image) : '',
        ];
    }
}
