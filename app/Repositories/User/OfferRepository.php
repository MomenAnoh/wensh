<?php

namespace App\Repositories\User;

use App\Enums\OfferStatus;
use App\Enums\UserTypes;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderOffer;
use App\Models\User;

class OfferRepository
{
    public function createOffer(Order $order, User $user, float $finalPrice): OrderOffer
    {
        $price =$order->price;
        $fees = $order->app_fee;

       $offer = OrderOffer::query()->create([
            'order_id' => $order->id,
            'made_from' => UserTypes::User,
            'price' => $price,
            'fees' => $fees,
            'total_price' => round($finalPrice, 2),
            'status' => OfferStatus::Pending,
        ]);
        OrderCreated::dispatch($offer);
        return $offer;
    }
}
