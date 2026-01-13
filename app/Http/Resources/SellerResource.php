<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "shop_name" => $this->seller->shop_name,
            "phone" => $this->seller->phone,
            "iban" => $this->seller->iban,
            "owner_name" => $this->name,
            "image" => $this->seller->logo ? asset('storage/products/' . $this->seller->logo) : '',
            "is_completed" => $this->seller->is_completed == 0 ? false : true,
            "verification_code" => null,
            "push_token" => $this->fcm_token,
            'is_verified' => $this->is_verified
        ];
    }
}
