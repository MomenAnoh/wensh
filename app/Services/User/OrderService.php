<?php

namespace App\Services\User;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\User\OfferRepository;
use App\Repositories\User\OrderRepository;

class OrderService
{
    protected $setting;

    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OfferRepository $offerRepository,
    ) {
        $this->setting = Setting::first();
    }

    public function create(User $user, array $data): Order
    {
        $data['user_id'] = $user->id;
        $data['app_fee'] = $this->setting->app_fee??0;
        $data['suggested_price'] = $this->calcSuggestedPrice($data);
        $finalPrice =$this->setting->app_fee >0 ? $data['price'] + ($data['price'] * $this->setting->app_fee??0 / 100) : $data['price'];
        $data['final_price'] = $finalPrice;
        $data['status'] = OrderStatus::Pending;

        $order = $this->orderRepository->create($data);

        $this->offerRepository->createOffer($order, $user, $finalPrice);



        return $order;
    }

    public function calcSuggestedPrice($data)
    {
        $km_price = $this->setting->km_price??0;
        $km_count = $this->calculateDistance($data['from_latitude'], $data['from_longitude'], $data['to_latitude'], $data['to_longitude']);
        $suggestedPrice = $km_count * $km_price;
        $data = [
            'suggested_price' => round($suggestedPrice, 2),
'tax_fee' => $this->setting?->app_fee ?? 0,        ];

        return $data;
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
