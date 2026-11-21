<?php

use App\Http\Controllers\Api\Driver\AuthController;
use App\Http\Controllers\Api\Driver\OrderOfferController;
use App\Http\Middleware\SetApiLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetApiLocale::class)
    ->prefix('v1/driver')
    ->name('api.v1.driver.')
    ->group(function (): void {
        Route::prefix('auth')->name('auth.')->group(function (): void {
            Route::post('/register', [AuthController::class, 'register'])->name('register');
            Route::post('/login', [AuthController::class, 'login'])->name('login');
        });

        Route::middleware('auth:wensh')->group(function (): void {
            Route::post('/offers', [OrderOfferController::class, 'store'])
                ->name('offers.store');
            Route::patch('/offers/{offer}/accept', [OrderOfferController::class, 'accept'])
                ->name('offers.accept');
        });
    });
