<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $message,
        private readonly string $chatId,
        private readonly ?string $parseMode = null,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $params = [
            'text'=> $this->message,
            'chat_id' => $this->chatId
        ];

        if ($this->parseMode !== null) {
            $params['parse_mode'] = $this->parseMode;
        }

        Telegram::sendMessage($params);
    }
}
