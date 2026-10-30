<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
        private readonly OtpDeliveryService $otpDeliveryService,
    ) {}

    /**
     * @return array{expires_in: int, otp: string|null}
     */
    public function sendOtp(string $phone): array
    {
        $rateLimitKey = 'otp-send:'.hash('sha256', $phone);
        $cooldownSeconds = (int) config('auth_otp.cooldown_seconds');

        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            abort(429);
        }

        $code = (string) config('auth_otp.fixed_code');
        RateLimiter::hit($rateLimitKey, $cooldownSeconds);
        $this->otpDeliveryService->deliver($phone, $code);
        $this->authRepository->createOtpChallenge(
            $phone,
            Hash::make($code),
            now()->addSeconds((int) config('auth_otp.expires_in')),
        );

        return [
            'expires_in' => (int) config('auth_otp.expires_in'),
            'otp' => app()->environment(['local', 'testing']) ? $code : null,
        ];
    }

    /**
     * @return array{token: string, user: User}
     */
    public function verifyOtp(string $phone, string $code): array
    {
        $challenge = $this->authRepository->findOtpChallenge($phone);

        if (
            $challenge === null
            || $challenge->consumed_at !== null
            || $challenge->expires_at->isPast()
            || $challenge->attempts >= (int) config('auth_otp.max_attempts')
        ) {
            $this->invalidOtp();
        }

        $this->authRepository->incrementAttempts($challenge);

        if (! Hash::check($code, $challenge->code_hash)) {
            $this->invalidOtp();
        }

        if (! $this->authRepository->consumeOtpChallenge($challenge)) {
            $this->invalidOtp();
        }

        $user = $this->authRepository->findOrCreateVerifiedUser($phone);

        return [
            'token' => auth('api')->login($user),
            'user' => $user,
        ];
    }

    private function invalidOtp(): never
    {
        throw ValidationException::withMessages([
            'code' => __('api.auth.invalid_otp'),
        ]);
    }
}
