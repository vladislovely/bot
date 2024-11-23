<?php

namespace App\Listeners;

use App\Events\HistoryUpdate;
use App\Telegram\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreHistoryListener
{
    /**
     * Handle the event.
     */
    public function handle(HistoryUpdate $event): void
    {
        if (!empty($event->messageIds)) {
            foreach ($event->messageIds as $id => $message) {
                TelegramService::saveHistory(
                    user_id: $event->user_id,
                    action: $event->action,
                    chatId: $event->chatId,
                    messageId: $id,
                    message: $message,
                );
            }
        }
    }
}
