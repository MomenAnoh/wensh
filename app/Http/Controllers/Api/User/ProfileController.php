<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Services\User\ProfileService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService) {}

    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(
            __('api.profile.loaded'),
            UserResource::make($request->user('api'))->resolve($request),
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->update($request->user('api'), $request->validated());

        return ApiResponse::success(
            __('api.profile.updated'),
            UserResource::make($user)->resolve($request),
        );
    }

    public function skip(Request $request): JsonResponse
    {
        $user = $this->profileService->skip($request->user('api'));

        return ApiResponse::success(
            __('api.profile.skipped'),
            UserResource::make($user)->resolve($request),
        );
    }
}
