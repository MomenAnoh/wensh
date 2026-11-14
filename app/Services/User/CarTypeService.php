<?php

namespace App\Services\User;

use App\Repositories\User\CarTypeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CarTypeService
{
    public function __construct(private readonly CarTypeRepository $carTypeRepository) {}

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->carTypeRepository->paginate($perPage);
    }
}
