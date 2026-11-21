<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderOfferResource extends JsonResource
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
            'order_id' => $this->order_id,
            'made_from' => $this->made_from,
            'price' => $this->price,
            'fees' => $this->fees,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'wensh_id' => $this->wensh_id,
            'created_at' => $this->created_at,
        ];
    }
}
