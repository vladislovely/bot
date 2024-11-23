<?php

namespace App\Telegram;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Button;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Payments\LabeledPrice;

class TelegramCaller
{
    public const string  CHOSEN_EN            = 'CHOSEN_EN';
    public const string  CHOSEN_RU            = 'CHOSEN_RU';
    public const string  CALL_CHOICE_LANGUAGE = 'CALL_CHOICE_LANGUAGE';
    public const string  CALL_WELCOME_MESSAGE = 'CALL_WELCOME_MESSAGE';
    public const string  CALL_BUY_PRODUCT     = 'CALL_BUY_PRODUCT';
    private const string PARSE_MODE_HTML      = 'HTML';

    public static function callChoiceLanguage(string $chatId): array
    {
        $keyboard = Keyboard::make()
            ->setOneTimeKeyboard(true)
            ->setResizeKeyboard(true)
            ->inline()
            ->row([
                Keyboard::inlineButton(['text'          => __('messages.buttonEn'),
                                        'callback_data' => self::CHOSEN_EN,
                ]),
                Keyboard::inlineButton(['text'          => __('messages.buttonRu'),
                                        'callback_data' => self::CHOSEN_RU,
                ]),
            ]);

        $response = Telegram::sendMessage([
            'chat_id'      => $chatId,
            'text'         => __('messages.language'),
            'reply_markup' => $keyboard,
        ]);

        return [$response->getMessageId() => __('messages.language')];
    }

    public static function callWelcomeMessage(string $chatId, string $from): array
    {
        $keyboard = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->inline()
            ->row([
                Keyboard::inlineButton([
                    'text'          => __('messages.buttonBuy'),
                    'callback_data' => self::CALL_BUY_PRODUCT,
                ]),
            ]);

        $response = Telegram::sendMessage(
            [
                'chat_id'      => $chatId,
                'text'         => __('messages.welcome', ['name' => $from]),
                'reply_markup' => $keyboard,
                'parse_mode'   => self::PARSE_MODE_HTML,
            ],
        );

        return [
            $response->getMessageId() => __('messages.welcome', ['name' => $from]),
        ];
    }

    public static function callSuccessPayment(string $chatId): array
    {
        $response = Telegram::sendMessage(
            [
                'chat_id' => $chatId,
                'text'    => __('messages.successPayment'),
            ],
        );

        return [
            $response->getMessageId()       => __('messages.successPayment'),
        ];
    }

    public static function callFailedPayment(string $chatId): array
    {
        $response = Telegram::sendMessage(
            [
                'chat_id' => $chatId,
                'text'    => __('messages.failedPayment'),
            ],
        );

        return [$response->getMessageId() => __('messages.failedPayment')];
    }

    public static function callInvoice(
        string $chatId,
        LabeledPrice $price,
        string $payload,
    ): array
    {
        $response = Telegram::sendInvoice([
            'chat_id'                => $chatId,
            'title'                  => __('messages.productTitle'),
            'description'            => __('messages.productDescription'),
            'provider_token'         => env('TELEGRAM_BOT_PAYMENTS_TOKEN'),
            'payload'                => $payload,
            'need_email'             => true,
            'send_email_to_provider' => true,
            'currency'               => 'RUB',
            'prices'                 => [
                $price,
            ],
        ]);

        return [
            $response->getMessageId() => 'invoice',
        ];
    }
}
