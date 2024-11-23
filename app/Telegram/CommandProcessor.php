<?php

namespace App\Telegram;

use App\Enums\PaymentStatus;
use App\Events\DeleteTelegramMessage;
use App\Events\HistoryUpdate;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Payments\LabeledPrice;

readonly class CommandProcessor
{
    public function __construct(
        private string $command,
        private Collection $message,
        private string $chatId
    )
    {
    }

    public function process(): void
    {
        $payload = env('TELEGRAM_PAYLOAD_TOKEN') ?? 'PAYLOAD_TOKEN';

        $from = $this->message->getChat()->getFirstName();
        $username = $this->message->getChat()->getUsername();
        $user = User::query()
            ->where(['username' => $username])
            ->get(['id', 'email'])
            ->first();

        switch ($this->command) {
            case TelegramCaller::CHOSEN_EN:
                Cache::put('language', 'en');
                App::setLocale('en');

                TelegramCaller::callWelcomeMessage(chatId: $this->chatId, from: $from);
                $action = TelegramCaller::CHOSEN_EN;

                break;
            case TelegramCaller::CHOSEN_RU:
                Cache::put('language', 'ru');
                App::setLocale('ru');

                $messageIds = TelegramCaller::callWelcomeMessage(chatId: $this->chatId, from: $from);
                $action = TelegramCaller::CHOSEN_RU;

                break;
            case TelegramCaller::CALL_CHOICE_LANGUAGE:
                $messageId = TelegramCaller::callChoiceLanguage(chatId: $this->chatId);
                $action = TelegramCaller::CALL_CHOICE_LANGUAGE;

                break;
            case TelegramCaller::CALL_WELCOME_MESSAGE:
                $messageIds = TelegramCaller::callWelcomeMessage(chatId: $this->chatId, from: $from);
                $action = TelegramCaller::CALL_WELCOME_MESSAGE;

                break;
            case TelegramCaller::CALL_BUY_PRODUCT:
                $payment = new Payment([
                    'user_id'  => $user->id,
                    'status'   => PaymentStatus::PENDING,
                    'payload'  => $payload,
                ]);
                $payment->save();

                $price = LabeledPrice::make(['label' => 'Тестовый продукт', 'amount' => 449 * 100]);

                $messageId = TelegramCaller::callInvoice(
                    chatId: $this->chatId,
                    price: $price,
                    payload: $payload
                );

                $action = TelegramCaller::CALL_BUY_PRODUCT;
        }

        event(new HistoryUpdate(
            messageIds: $messageIds ?? $messageId ?? [],
            action: $action,
            chatId: $this->chatId,
            user_id: $user->id,
        ));

        $messageIdentifiers = [];

        if (!empty($messageIds)) {
            $messageIdentifiers = array_keys($messageIds);
        }

        if (!empty($messageId)) {
            $messageIdentifiers = array_keys($messageId);
        }

        event(new DeleteTelegramMessage(
            chatId: $this->chatId,
            user_id: $user->id,
            exceptIds: $messageIdentifiers,
        ));
    }
}
