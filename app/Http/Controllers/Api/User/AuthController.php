<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Auth\SendOtpRequest;
use App\Http\Requests\Api\User\Auth\VerifyOtpRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Services\User\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $result = $this->authService->sendOtp($request->validated('phone'));
        $data = [
            'expires_in' => $result['expires_in'],
            'resend_after' => config('auth_otp.cooldown_seconds'),
        ];

        if ($result['otp'] !== null) {
            $data['otp'] = $result['otp'];
        }

        return ApiResponse::success(__('api.auth.otp_sent'), $data);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->validated('phone'),
            $request->validated('code'),
        );

        return ApiResponse::success(__('api.auth.verified'), [
            'access_token' => $result['token'],
            'token_type' => 'bearer',
            'expires_in' => null,
            'user' => UserResource::make($result['user'])->resolve($request),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            __('api.auth.user_loaded'),
            UserResource::make($request->user('api'))->resolve($request),
        );
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return ApiResponse::success(__('api.auth.logged_out'));
    }

    public function refresh(): JsonResponse
    {
        return ApiResponse::success(__('api.auth.refreshed'), [
            'access_token' => auth('api')->refresh(),
            'token_type' => 'bearer',
            'expires_in' => null,
        ]);
    }
}
