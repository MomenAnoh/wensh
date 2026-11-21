<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

#[Fillable([
    'full_name',
    'phone_code',
    'phone',
    'password',
    'photo',
    'current_latitude',
    'current_longitude',
    'is_active',
    'is_veryfaied',
    'lisence_image',
    'lisensce_number',
])]
#[Hidden(['password'])]
class Wensh extends Authenticatable implements JWTSubject
{
    protected function casts(): array
    {
        return [
            'current_latitude' => 'decimal:7',
            'current_longitude' => 'decimal:7',
            'is_active' => 'boolean',
            'is_veryfaied' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier(): int|string
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function images(): HasMany
    {
        return $this->hasMany(WenshImage::class, 'wesh_id');
    }
}
