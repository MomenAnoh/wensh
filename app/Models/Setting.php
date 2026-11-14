<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'app_fee',
    'km_price',
    'logo',
    'min_allowed_time_for_cancel',
    'cancel_fee',
])]
class Setting extends Model
{
    protected function casts(): array
    {
        return [
            'app_fee' => 'decimal:2',
            'km_price' => 'decimal:2',
            'min_allowed_time_for_cancel' => 'integer',
            'cancel_fee' => 'decimal:2',
        ];
    }
}
