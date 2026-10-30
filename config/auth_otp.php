<?php

return [
    'fixed_code' => env('OTP_FIXED_CODE', '123456'),
    'expires_in' => 300,
    'max_attempts' => 5,
    'cooldown_seconds' => 60,
];
