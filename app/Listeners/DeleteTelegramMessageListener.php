<?php

namespace App\Listeners;

use App\Events\DeleteTelegramMessage;
use App\Models\History;
use App\Telegram\TelegramCaller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Jobs\DeleteTelegramMessage as DeleteMessage;

class DeleteTelegramMessageListener
{
    /**
     * Handle the event.
     */
    public function handle(DeleteTelegramMessage $event): void
    {
        if (!empty($event->exceptIds)) {
            Log::debug('Should delete everything except this ids: ', $event->exceptIds);

            $histories = History::getNotDeletedMessages(
                chatId: $event->chatId,
                userId: $event->user_id,
                exceptIds: $event->exceptIds
            );
        }

        if (!empty($histories)) {
            foreach ($histories as $history) {
                DeleteMessage::dispatch(
                    chatId: $history['chat_id'],
                    messageId: $history['message_id'],
                );
            }
        }
    }
}
