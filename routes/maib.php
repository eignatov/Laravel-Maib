<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Nikba\LaravelMaib\Callback\CallbackController;

Route::post(
    config('maib.callback_route', '/maib/callback'),
    [CallbackController::class, 'handle']
)->name('maib.callback')->withoutMiddleware([
    Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    VerifyCsrfToken::class,
]);
