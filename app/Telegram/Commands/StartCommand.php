<?php

namespace App\Telegram\Commands;

use App\Models\User;
use App\Telegram\TelegramCaller;
use App\Telegram\TelegramService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = "Let's start using our bot!";

    public function handle(): void
    {
        $replyMarkup = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => __('messages.buttonEn'), 'callback_data' => TelegramCaller::CHOSEN_EN]),
                Keyboard::inlineButton(['text' => __('messages.buttonRu'), 'callback_data' => TelegramCaller::CHOSEN_RU]),
            ]);

        $message = $this->replyWithMessage([
            'text' => __('messages.language'),
            'reply_markup' => $replyMarkup
        ]);

        $user = User::firstOrCreate(
            ['username' => $message->getChat()->getUsername()],
            ['first_name' => $message->getChat()->getFirstName()],
        );

        TelegramService::saveHistory(
            user_id: $user->id,
            action: TelegramCaller::CALL_CHOICE_LANGUAGE,
            chatId: $message->getChat()->getId(),
            messageId: $message->getMessageId(),
        );
    }
}
