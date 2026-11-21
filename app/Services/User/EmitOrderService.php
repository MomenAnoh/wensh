<?php

namespace App\Services\User;

use App\Models\OrderOffer;
use Illuminate\Support\Facades\Http;

class EmitOrderService
{
    /**
     * @return array<string, mixed>
     */
    public function emitOffer(OrderOffer $offer): array
    {
        return Http::withToken(config('services.node.secret'))
            ->post(config('services.node.url').'/send-event', [
                'room' => 'wensh_1',
                'event' => 'receive_offer',
                'data' => [
                    'offer' => 'My Offer',
                    'offer_data' => $offer,
                ],
            ])
            ->json();
    }

    /**
     * @return array<string, mixed>
     */
    public function emitOfferAccepted(OrderOffer $offer): array
    {
        return Http::withToken(config('services.node.secret'))
            ->post(config('services.node.url').'/send-event', [
                'room' => 'user_'.$offer->user_id,
                'event' => 'offer_accepted',
                'data' => [
                    'offer' => 'Offer Accepted',
                    'offer_data' => $offer,
                ],
            ])
            ->json();
    }

    /**
     * @return array<string, mixed>
     */
    public function emitOfferToUser(OrderOffer $offer): array
    {
        return Http::withToken(config('services.node.secret'))
            ->post(config('services.node.url').'/send-event', [
                'room' => 'user_'.$offer->user_id,
                'event' => 'receive_offer',
                'data' => [
                    'offer' => 'New Driver Offer',
                    'offer_data' => $offer,
                ],
            ])
            ->json();
    }

    /**
     * @return array<string, mixed>
     */
    public function emitOfferToWensh(OrderOffer $offer): array
    {
        return Http::withToken(config('services.node.secret'))
            ->post(config('services.node.url').'/send-event', [
                'room' => 'wensh_'.$offer->wensh_id,
                'event' => 'receive_offer',
                'data' => [
                    'offer' => 'New User Offer',
                    'offer_data' => $offer,
                ],
            ])
            ->json();
    }
}
