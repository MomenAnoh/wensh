<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\ThisOrder;
use App\Services\User\EmitOrderService;

class BroadcastOrderToWenshes
{
    public function __construct(protected EmitOrderService $emitOrderService)
    {
        //
    }
    public function handle(OrderCreated $event): void
    {
        $this->emitOrderService->emitOffer($event->offer);

    }
}
