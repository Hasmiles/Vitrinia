<?php

namespace App\Http\Resources;

use App\Models\Option;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $sub_options = $this->whenLoaded('subOptions');
        return [
            'id' => $this->id,
            'seller_id' => $this->seller_id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'image_url' => $this->image ? asset('storage/products/' . $this->image) : '',
            'options' => $sub_options->groupBy('main_id')->map(function ($group) {
                $firstItem = $group->first();
                $groupName = $firstItem->mainOption ? $firstItem->mainOption->title : 'Diğer';
                return [
                    'name' => $groupName,
                    'values' => $group->pluck('title')
                ];
            })->values(),
        ];
    }
}
