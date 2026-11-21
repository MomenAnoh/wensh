<?php

return [
    'attributes' => [
        'code' => 'verification code',
        'email' => 'email address',
        'emergency_contact_phone' => 'emergency contact phone',
        'enabled' => 'notification status',
        'name' => 'full name',
        'phone' => 'phone number',
        'profile_photo' => 'profile photo',
    ],
    'auth' => [
        'invalid_otp' => 'The verification code is invalid or has expired.',
        'logged_out' => 'You have been logged out.',
        'otp_sent' => 'The verification code has been sent.',
        'rate_limited' => 'Please wait before requesting another verification code.',
        'refreshed' => 'The access token has been refreshed.',
        'unauthenticated' => 'You must be signed in to continue.',
        'user_loaded' => 'User data loaded successfully.',
        'verified' => 'The phone number was verified successfully.',
    ],
    'content' => [
        'loaded' => 'Content loaded successfully.',
        'not_found' => 'The requested content was not found.',
    ],
    'driver' => [
        'invalid_credentials' => 'The phone number or password is incorrect.',
        'logged_in' => 'Driver signed in successfully.',
        'registered' => 'Driver registered successfully.',
    ],
    'car_types' => [
        'loaded' => 'Car types loaded successfully.',
    ],
    'errors' => [
        'forbidden' => 'You are not authorized to perform this action.',
        'not_found' => 'The requested resource was not found.',
        'request_failed' => 'The request could not be completed.',
        'server_error' => 'An internal error occurred. Please try again later.',
    ],
    'notifications' => [
        'updated' => 'Notification preferences were updated.',
    ],
    'orders' => [
        'accepted' => 'Offer accepted and order prices updated successfully.',
        'counter_offer_sent' => 'Counter offer sent to the driver successfully.',
        'created' => 'Order created successfully.',
        'offer_sent' => 'Offer sent to the customer successfully.',
        'price_calculated' => 'Suggested price calculated successfully.',
    ],
    'profile' => [
        'loaded' => 'Profile data loaded successfully.',
        'skipped' => 'You can complete your profile at any time.',
        'updated' => 'Your profile was saved successfully.',
    ],
    'validation_failed' => 'The request data is invalid.',
];
