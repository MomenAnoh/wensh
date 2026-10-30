<?php

namespace App\Services\User;

use LogicException;

class OtpDeliveryService
{
    public function deliver(string $phone, string $code): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('An SMS provider must be configured before sending OTPs outside local and testing environments.');
        }

        if (! hash_equals((string) config('auth_otp.fixed_code'), $code)) {
            throw new LogicException('The development OTP does not match the configured fixed code.');
        }
    }
}
