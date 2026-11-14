<?php

use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\CarTypeController;
use App\Http\Controllers\Api\User\ContentController;
use App\Http\Controllers\Api\User\NotificationPreferenceController;
use App\Http\Controllers\Api\User\OrderController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Middleware\SetApiLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetApiLocale::class)
    ->prefix('v1/user')
    ->name('api.v1.user.')
    ->group(function (): void {
        Route::prefix('auth')->name('auth.')->group(function (): void {
            Route::post('/otp/send', [AuthController::class, 'sendOtp'])
                ->middleware('throttle:10,1')
                ->name('otp.send');
            Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])
                ->middleware('throttle:10,1')
                ->name('otp.verify');

            Route::middleware('auth:api')->group(function (): void {
                Route::get('/me', [AuthController::class, 'me'])->name('me');
                Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
                Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
            });
        });

        Route::prefix('content')->name('content.')->controller(ContentController::class)->group(function (): void {
            Route::get('/terms-and-conditions', 'termsAndConditions')->name('terms-and-conditions');
            Route::get('/privacy-policy', 'privacyPolicy')->name('privacy-policy');
            Route::get('/about-us', 'aboutUs')->name('about-us');
        });

        Route::get('/car-types', [CarTypeController::class, 'index'])->name('car-types.index');

        Route::middleware('auth:api')->group(function (): void {
            Route::prefix('order')->name('order.')->group(function (): void {
                Route::post('/', [OrderController::class, 'store'])->name('store');
                Route::post('/calc-suggested-price', [OrderController::class, 'calcSuggestedPrice'])
                    ->name('calc-suggested-price');
            });

            Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/skip', [ProfileController::class, 'skip'])->name('profile.skip');

            Route::patch('/notifications/requests', [NotificationPreferenceController::class, 'updateRequests'])
                ->name('notifications.requests');
            Route::patch('/notifications/offers', [NotificationPreferenceController::class, 'updateOffers'])
                ->name('notifications.offers');
        });
    });
