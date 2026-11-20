<?php

namespace App\Repositories\User;

use App\Models\Order;

class OrderRepository
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Order
    {
        
        return Order::query()->create($attributes);
    }
}
