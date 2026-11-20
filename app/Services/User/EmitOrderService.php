<?php

namespace App\Services\User;

use App\Enums\UserTypes;
use App\Models\OrderOffer;
use Illuminate\Support\Facades\Http;

class EmitOrderService
{
    /**
     * @return array<string, mixed>
     */
    public function emitOffer(OrderOffer $offer): array
    {
        $room = match ($offer->made_from) {
            UserTypes::User => 'user_'.$offer->user_id,
            UserTypes::Wensh => 'wench_'.$offer->wensh_id,
        };

        return Http::withToken(config('services.node.secret'))
            ->post(config('services.node.url').'/send-event', [
                'room' => $room,
                'event' => 'receive_offer',
                'data' => [
                    'offer' => 'My Offer',
                    'final_price' => $offer->total_price,
                ],
            ])
            ->json();
    }
}
