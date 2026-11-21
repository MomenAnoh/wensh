<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\OfferStatus;
use App\Enums\OrderStatus;
use App\Enums\UserTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Order\StoreCounterOfferRequest;
use App\Http\Resources\Api\User\OrderOfferResource;
use App\Models\Order;
use App\Models\OrderOffer;
use App\Services\User\EmitOrderService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderOfferController extends Controller
{
    public function storeCounterOffer(
        StoreCounterOfferRequest $request,
        EmitOrderService $emitOrderService,
    ): JsonResponse {
        $data = $request->validated();
        $userId = $request->user('api')->id;

        $offer = DB::transaction(function () use ($data, $userId): ?OrderOffer {
            $order = Order::query()
                ->whereKey($data['order_id'])
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== OrderStatus::Pending) {
                return null;
            }

            $driverOffer = $order->offers()
                ->where('made_from', UserTypes::Wensh)
                ->orderByDesc('id')
                ->first();

            if ($driverOffer?->wensh_id === null) {
                return null;
            }

            $price = (float) $data['price'];
            $feePercentage = (float) $order->app_fee;
            $totalPrice = round($price + ($price * $feePercentage / 100), 2);

            return OrderOffer::query()->create([
                'order_id' => $order->id,
                'made_from' => UserTypes::User,
                'price' => $price,
                'fees' => $feePercentage,
                'total_price' => $totalPrice,
                'status' => OfferStatus::Pending,
                'user_id' => $userId,
                'wensh_id' => $driverOffer->wensh_id,
            ]);
        });

        if ($offer === null) {
            return ApiResponse::error(__('api.errors.request_failed'), 422);
        }

        $emitOrderService->emitOfferToWensh($offer);

        return ApiResponse::success(
            __('api.orders.counter_offer_sent'),
            OrderOfferResource::make($offer)->resolve($request),
            201,
        );
    }
}
