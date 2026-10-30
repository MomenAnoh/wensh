<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Notification\UpdatePreferenceRequest;
use App\Services\User\NotificationPreferenceService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class NotificationPreferenceController extends Controller
{
    public function __construct(private readonly NotificationPreferenceService $preferenceService) {}

    public function updateRequests(UpdatePreferenceRequest $request): JsonResponse
    {
        $user = $this->preferenceService->update(
            $request->user('api'),
            'requests_notifications_enabled',
            $request->boolean('enabled'),
        );

        return ApiResponse::success(__('api.notifications.updated'), [
            'requests_enabled' => $user->requests_notifications_enabled,
        ]);
    }

    public function updateOffers(UpdatePreferenceRequest $request): JsonResponse
    {
        $user = $this->preferenceService->update(
            $request->user('api'),
            'offers_notifications_enabled',
            $request->boolean('enabled'),
        );

        return ApiResponse::success(__('api.notifications.updated'), [
            'offers_enabled' => $user->offers_notifications_enabled,
        ]);
    }
}
