<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:register');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:password-reset');

    Route::post('/reset-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:password-reset');

    Route::post('/two-factor-challenge', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
        ->middleware('throttle:two-factor');
});
