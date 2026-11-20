<?php

namespace App\Services\User;

use Illuminate\Support\Facades\Http;

class EmitOrderService
{
    public function emitOffer($offer)
    {
        dd($offer);
        $room=$offer->made_from== 'user' ? 'user_'.$offer->user_id :'wench_'.$offer->wensh;
        $response = Http::withToken(config('services.node.secret'))
        ->post(config('services.node.url') . '/send-event', [
            'room' => $room,
            'event' => 'receive_offer',
            'data' => [
                'offer' => 'My Offer',
                'price' => 500,
            ],
        ]);

      return $response->json();

    }
}
