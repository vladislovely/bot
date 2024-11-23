<?php

namespace App\Telegram;

use App\Models\History;
use Illuminate\Http\JsonResponse;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function getMe(): JsonResponse
    {
        $response = Telegram::getMe();

        return response()->json($response);
    }

    public function setWebhook(): JsonResponse
    {
        $url = env('APP_URL')
               . '/webhook'
               . '/telegram'
               . '/' . env('TELEGRAM_BOT_TOKEN')
               . '/message';

        $result = Telegram::setWebhook(
            [
                'url'         => $url,
            ],
        );

        return response()->json([
            'status' => $result,
        ]);
    }

    public function removeWebhook(): JsonResponse
    {
        $result = Telegram::removeWebhook();

        return response()->json([
            'status' => $result,
        ]);
    }

    /**
     * @throws \JsonException
     */
    public static function saveHistory(
        string $user_id,
        ?string $action,
        string $chatId,
        string $messageId,
        array $additionalInformation = [],
        ?string $message = null,
    ): bool
    {
        $model = History::updateOrCreate(
            [
                'chat_id' => $chatId,
                'user_id' => $user_id,
                'message_id' => $messageId
            ],
            [
                'action' => $action,
                'message' => $message,
                'additionalInformation' => json_encode($additionalInformation, JSON_THROW_ON_ERROR),
            ],
        );

        return $model instanceof History;
    }
}
