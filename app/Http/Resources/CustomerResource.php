<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'fullname' => $this->fullname,
            'phone' => $this->phone,
            'city' => $this->city,
            'town' => $this->town,
            'address' => $this->address,
            'address_description' => $this->address_2,
            'note' => $this->note
        ];
    }
}
