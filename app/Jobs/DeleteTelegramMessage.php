<?php

namespace App\Jobs;

use App\Models\History;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class DeleteTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $messageId,
        private readonly string $chatId,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Telegram::deleteMessage(['chat_id' => $this->chatId, 'message_id' => $this->messageId]);

            History::query()->where(['chat_id' => $this->chatId, 'message_id' => $this->messageId])
                ->update(['is_deleted' => 1]);
        } catch (\Throwable $exception) {
            Log::debug('Can not delete history: ' . $exception->getMessage());

            return;
        }
    }
}
