<?php

namespace App\Repositories\User;

use App\Models\OtpChallenge;
use App\Models\User;
use Illuminate\Support\Carbon;

class AuthRepository
{
    public function createOtpChallenge(string $phone, string $codeHash, Carbon $expiresAt): OtpChallenge
    {
        return OtpChallenge::query()->updateOrCreate(
            ['phone' => $phone],
            [
                'code_hash' => $codeHash,
                'expires_at' => $expiresAt,
                'attempts' => 0,
                'consumed_at' => null,
            ],
        );
    }

    public function findOtpChallenge(string $phone): ?OtpChallenge
    {
        return OtpChallenge::query()->where('phone', $phone)->first();
    }

    public function incrementAttempts(OtpChallenge $challenge): void
    {
        $challenge->increment('attempts');
    }

    public function consumeOtpChallenge(OtpChallenge $challenge): bool
    {
        return OtpChallenge::query()
            ->whereKey($challenge->getKey())
            ->whereNull('consumed_at')
            ->update([
                'consumed_at' => now(),
                'updated_at' => now(),
            ]) === 1;
    }

    public function findOrCreateVerifiedUser(string $phone): User
    {
        $user = User::query()->firstOrCreate(
            ['phone' => $phone],
            ['phone_verified_at' => now()],
        );

        if ($user->phone_verified_at === null) {
            $user->forceFill(['phone_verified_at' => now()])->save();
        }

        return $user->refresh();
    }
}
