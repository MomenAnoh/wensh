<?php

namespace App\Http\Controllers\Api\Driver;

use App\Enums\OfferStatus;
use App\Enums\OrderStatus;
use App\Enums\UserTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Driver\StoreOrderOfferRequest;
use App\Http\Resources\Api\User\OrderOfferResource;
use App\Models\Order;
use App\Models\OrderOffer;
use App\Services\User\EmitOrderService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderOfferController extends Controller
{
    public function store(StoreOrderOfferRequest $request, EmitOrderService $emitOrderService): JsonResponse
    {
        $data = $request->validated();
        $wenshId = $request->user('wensh')->id;

        $offer = DB::transaction(function () use ($data, $wenshId): ?OrderOffer {
            $order = Order::query()
                ->whereKey($data['order_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== OrderStatus::Pending) {
                return null;
            }

            $price = (float) $data['price'];
            $feePercentage = (float) $order->app_fee;
            $totalPrice = round($price + ($price * $feePercentage / 100), 2);

            return OrderOffer::query()->create([
                'order_id' => $order->id,
                'made_from' => UserTypes::Wensh,
                'price' => $price,
                'fees' => $feePercentage,
                'total_price' => $totalPrice,
                'status' => OfferStatus::Pending,
                'user_id' => $order->user_id,
                'wensh_id' => $wenshId,
            ]);
        });

        if ($offer === null) {
            return ApiResponse::error(__('api.errors.request_failed'), 422);
        }

        $emitOrderService->emitOfferToUser($offer);

        return ApiResponse::success(
            __('api.orders.offer_sent'),
            OrderOfferResource::make($offer)->resolve($request),
            201,
        );
    }

    public function accept(OrderOffer $offer, EmitOrderService $emitOrderService): JsonResponse
    {
        $acceptedOffer = DB::transaction(function () use ($offer): ?OrderOffer {
            $lockedOffer = OrderOffer::query()
                ->whereKey($offer->getKey())
                ->lockForUpdate()
                ->firstOrFail();
            $order = $lockedOffer->order()->lockForUpdate()->firstOrFail();

            if (
                $lockedOffer->made_from !== UserTypes::User ||
                $lockedOffer->status !== OfferStatus::Pending ||
                $order->status !== OrderStatus::Pending ||
                ($lockedOffer->wensh_id !== null && $lockedOffer->wensh_id !== auth('wensh')->id())
            ) {
                return null;
            }

            $lockedOffer->update([
                'status' => OfferStatus::Accepted,
                'wensh_id' => auth('wensh')->id(),
            ]);
            $order->update([
                'status' => OrderStatus::Accepted,
                'price' => $lockedOffer->price,
                'app_fee' => $lockedOffer->fees,
                'final_price' => $lockedOffer->total_price,
            ]);

            OrderOffer::query()
                ->where('order_id', $order->id)
                ->where('id', '!=', $lockedOffer->getKey())
                ->where('status', OfferStatus::Pending)
                ->update(['status' => OfferStatus::Rejected]);

            return $lockedOffer->fresh();
        });

        if ($acceptedOffer === null) {
            return ApiResponse::error(__('api.errors.request_failed'), 422);
        }

        $emitOrderService->emitOfferAccepted($acceptedOffer);

        return ApiResponse::success(__('api.orders.accepted'), [
            'offer_id' => $acceptedOffer->id,
            'order_id' => $acceptedOffer->order_id,
            'offer_status' => $acceptedOffer->status,
            'order_status' => OrderStatus::Accepted,
            'price' => $acceptedOffer->price,
            'fees' => $acceptedOffer->fees,
            'total_price' => $acceptedOffer->total_price,
        ]);
    }
}
