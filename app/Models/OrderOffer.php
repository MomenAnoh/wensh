<?php

namespace App\Models;

use App\Enums\OfferStatus;
use App\Enums\UserTypes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'made_from', 'price', 'fees', 'total_price', 'status', 'user_id', 'wensh_id'])]
class OrderOffer extends Model
{
    protected function casts(): array
    {
        return [
            'made_from' => UserTypes::class,
            'price' => 'decimal:2',
            'fees' => 'decimal:2',
            'total_price' => 'decimal:2',
            'status' => OfferStatus::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
