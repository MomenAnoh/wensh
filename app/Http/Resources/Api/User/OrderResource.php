<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'car_type_id' => $this->car_type_id,
            'from_latitude' => $this->from_latitude,
            'from_longitude' => $this->from_longitude,
            'to_latitude' => $this->to_latitude,
            'to_longitude' => $this->to_longitude,
            'type' => $this->type,
            'status' => $this->status,
            'scheduled_day' => $this->scheduled_day?->toDateString(),
            'scheduled_time' => $this->scheduled_time,
            'payment_methods' => $this->payment_methods,
            'suggested_price' => $this->suggested_price,
            'discount' => $this->discount,
            'app_fee' => $this->app_fee,
            'final_price' => $this->final_price,
            'offers' => OrderOfferResource::collection($this->whenLoaded('offers')),
        ];
    }
}
