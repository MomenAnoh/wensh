<?php

namespace App\Services\User;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\User\OfferRepository;
use App\Repositories\User\OrderRepository;

class OrderService
{
    private ?Setting $setting;

    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OfferRepository $offerRepository,
    ) {
        $this->setting = Setting::first();
    }

    public function create(User $user, array $data): Order
    {
        $appFee = (float) ($this->setting?->app_fee ?? 0);

        $data['user_id'] = $user->id;
        $data['app_fee'] = $appFee;
        $suggestedPrice = $this->calcSuggestedPrice($data);
        $data['suggested_price'] = $suggestedPrice['suggested_price'];
        $finalPrice = $data['price'] + ($data['price'] * $appFee / 100);
        $data['final_price'] = $finalPrice;
        $data['status'] = OrderStatus::Pending;

        $order = $this->orderRepository->create($data);

        $this->offerRepository->createOffer($order, $user, $finalPrice);

        return $order;
    }

    /**
     * @param  array{from_latitude: float|int|string, from_longitude: float|int|string, to_latitude: float|int|string, to_longitude: float|int|string}  $data
     * @return array{suggested_price: float, tax_fee: float}
     */
    public function calcSuggestedPrice(array $data): array
    {
        $kmPrice = (float) ($this->setting?->km_price ?? 0);
        $kmCount = $this->calculateDistance(
            (float) $data['from_latitude'],
            (float) $data['from_longitude'],
            (float) $data['to_latitude'],
            (float) $data['to_longitude'],
        );
        $suggestedPrice = $kmCount * $kmPrice;

        return [
            'suggested_price' => round($suggestedPrice, 2),
            'tax_fee' => (float) ($this->setting?->app_fee ?? 0),
        ];
    }

    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
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
