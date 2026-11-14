<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\User\CarTypeResource;
use App\Services\User\CarTypeService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarTypeController extends Controller
{
    public function __construct(private readonly CarTypeService $carTypeService) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 50);
        $carTypes = $this->carTypeService->paginate($perPage);

        return ApiResponse::success(
            __('api.car_types.loaded'),
            CarTypeResource::collection($carTypes)->response()->getData(true),
        );
    }
}
