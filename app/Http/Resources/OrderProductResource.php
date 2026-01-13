<?php

namespace App\Http\Resources;

use App\Models\SubOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->relationLoaded('product') ? $this->product : null;
        $subOptions = ($product && $product->relationLoaded('subOptions')) 
                    ? $product->subOptions 
                    : collect([]);
        $selected_options = $this->relationLoaded('option') ? $this->option->pluck('option_id') : null;
        $option = SubOption::whereIn('id',$selected_options)->with('mainOption')->get();
        $formattedOptions = $option->pluck('title', 'mainOption.title');
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'seller_id' => $product->seller_id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'image_url' =>  $product->image ? asset('storage/products/' . $product->image) : '',  
            'options' => $subOptions->groupBy('main_id')->map(function ($group) {
                $firstItem = $group->first();
                $groupName = $firstItem->mainOption ? $firstItem->mainOption->title : 'Diğer';
                return [
                    'name' => $groupName,
                    'values' => $group->pluck('title')
                ];
            })->values(),// Dolu olunca çağırılıyor. n + 1 kısımı düzeltilmiş oluyor.
            'selected_options' => $formattedOptions,
        ];
    }
}
