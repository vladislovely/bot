<?php

use App\Http\Controllers\TelegramHookController;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return 'Hello It Is Your Telegram Support Microservice, Welcome!';
});

Route::group(['prefix' => 'webhook'], static function ($router) {
    Route::group(['prefix' => 'telegram'], static function ($router) {
        Route::group(['prefix' => '{token}'], static function ($router) {
            Route::post('/message', [TelegramHookController::class, 'receiveMessage']);
        });
    });
});
