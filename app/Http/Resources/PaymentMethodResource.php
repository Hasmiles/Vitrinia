<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $typeId = $this->type;


        $meta = match ($typeId) {
            1 => ['name' => 'Nakit', 'color' => '#10B981'],
            2 => ['name' => 'IBAN / Transfer', 'color' => '#3B82F6'],
            default => ['name' => 'Diğer', 'color' => '#9CA3AF'],
        };

        return [
            'name'  => $meta['name'],
            'value' => $this->total,
            'color' => $meta['color'],
        ];
    }
}
