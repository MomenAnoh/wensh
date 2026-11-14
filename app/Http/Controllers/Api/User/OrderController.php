<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Order\CalcSuggestedPriceRequest;
use App\Http\Requests\Api\User\Order\StoreOrderRequest;
use App\Http\Resources\Api\User\OrderResource;
use App\Services\User\OrderService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->user('api'), $request->validated());

        return ApiResponse::success(
            __('api.orders.created'),
            OrderResource::make($order)->resolve($request),
            201,
        );
    }

    public function calcSuggestedPrice(CalcSuggestedPriceRequest $request): JsonResponse
    {
        $data = $this->orderService->calcSuggestedPrice($request->validated());

        return ApiResponse::success(
            __('api.orders.price_calculated'),
            $data,
        );
    }
}
