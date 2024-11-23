<?php

use App\Telegram\TelegramService;
use Illuminate\Support\Facades\Route;

Route::get('/app-url', static function () {
    return response()->json([
        'url' => env('APP_URL'),
    ]);
});

Route::get('/', static function () {
    return response()->json([
        'name' => 'Abigail',
        'state' => 'CA',
    ]);
});

Route::get('/me', static function () {
    return (new TelegramService())->getMe();
});

Route::get('/telegram/set-webhook', static function () {
    return (new TelegramService())->setWebhook();
});

Route::get('/telegram/remove-webhook', static function () {
    return (new TelegramService())->removeWebhook();
});
