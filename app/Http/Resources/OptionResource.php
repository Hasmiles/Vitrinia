<?php

namespace App\Http\Resources;

use App\Models\Option;
use App\Models\SubOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
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
            'label' => $this->title,
            'type' => $this->title == 'Renk' ? 'color' : 'text',
            'values' => SubOptionResource::collection($this->sub_option)
        ];
    }
}
