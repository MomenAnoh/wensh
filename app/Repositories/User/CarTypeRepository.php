<?php

namespace App\Repositories\User;

use App\Models\CarType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CarTypeRepository
{
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return CarType::query()
            ->orderBy('id')
            ->paginate($perPage);
    }
}
