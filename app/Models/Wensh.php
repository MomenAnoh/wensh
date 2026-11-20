<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'full_name',
    'phone_code',
    'phone',
    'photo',
    'current_latitude',
    'current_longitude',
    'is_active',
    'is_veryfaied',
    'lisence_image',
    'lisensce_number',
])]
class Wensh extends Model
{
    protected function casts(): array
    {
        return [
            'current_latitude' => 'decimal:7',
            'current_longitude' => 'decimal:7',
            'is_active' => 'boolean',
            'is_veryfaied' => 'boolean',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(WenshImage::class, 'wesh_id');
    }
}
