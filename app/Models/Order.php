<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'car_type_id',
    'from_latitude',
    'from_longitude',
    'to_latitude',
    'to_longitude',
    'type',
    'status',
    'scheduled_day',
    'scheduled_time',
    'payment_methods',
    'suggested_price',
    'discount',
    'app_fee',
    'price',
    'final_price',
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'scheduled_day' => 'date',
             'suggested_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'app_fee' => 'decimal:2',
            'price' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function carType(): BelongsTo
    {
        return $this->belongsTo(CarType::class);
    }
}
