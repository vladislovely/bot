<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\User;
use App\Telegram\CommandProcessor;
use App\Telegram\TelegramCaller;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramHookController extends Controller
{
    private ?string $command = null;

    final public function receiveMessage(string $token, Request $request): void
    {
        Telegram::commandsHandler(true);

        $updates = Telegram::getWebhookUpdate();
        $isBot = $updates->getMessage()->getFrom()->isBot;

        if ($isBot) {
            $data = $updates->get('callback_query')->get('data');
            $chatId = $updates->getMessage()->getChat()->getId();
            $this->command = $data;

            Log::debug('Message', [$updates->getMessage()]);
        }

        if (!$isBot && $updates->getPreCheckoutQuery() !== null) {
            $payload = $updates->getPreCheckoutQuery()->getInvoicePayload();

            $appPayloadToken = env('TELEGRAM_PAYLOAD_TOKEN') ?? 'PAYLOAD_TOKEN';
            $email = $updates->getPreCheckoutQuery()->getOrderInfo()->getEmail();

            if ($payload === $appPayloadToken) {
                Telegram::answerPreCheckoutQuery([
                    'pre_checkout_query_id' => $updates->getPreCheckoutQuery()->getId(),
                    'ok'                    => true,
                ]);
            } else {
                Telegram::answerPreCheckoutQuery([
                    'pre_checkout_query_id' => $updates->getPreCheckoutQuery()->getId(),
                    'ok'                    => false,
                ]);
            }

            Log::debug('preCheckoutQuery', [
                'query'   => $updates->getPreCheckoutQuery(),
                'payload' => $payload,
                'email'   => $email,
            ]);
        }

        if (!$isBot && !empty($updates->getMessage()->getSuccessfulPayment())) {
            $chatId = $updates->getMessage()->getChat()->getId();
            $username = $updates->getMessage()->getFrom()->getUsername();
            $email = $updates->getMessage()->getSuccessfulPayment()->getOrderInfo()->getEmail();
            $user = User::query()->where('username', $username)->first();

            if ($user && empty($user->email)) {
                User::where('username', $username)
                    ->update(['email' => $email]);

                $user->payment()->update(['status' => PaymentStatus::PAID]);
            }

            TelegramCaller::callSuccessPayment(chatId: $chatId);
        }

        if ($this->command !== null) {
            (new CommandProcessor(
                command: $this->command,
                message: $updates->getMessage(),
                chatId: $chatId,
            ))->process();
        }

        Log::debug('Log', [
            'isBot'    => $isBot,
            'command'  => $data ?? [],
            'message'  => $updates->getMessage(),
            'checkout' => $updates->getPreCheckoutQuery(),
            'payment'  => $updates->getSuccessfulPayment(),
        ]);
    }
}
