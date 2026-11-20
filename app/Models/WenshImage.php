<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['image', 'wesh_id'])]
class WenshImage extends Model
{
    public function wensh(): BelongsTo
    {
        return $this->belongsTo(Wensh::class, 'wesh_id');
    }
}
