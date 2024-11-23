<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class History extends Model
{
    protected $table = 'history';

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'user_id',
        'action',
        'chat_id',
        'message_id',
        'payload',
        'message',
        'is_deleted',
        'additional_information',
    ];

    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getNotDeletedMessages(int $chatId, string $userId, array $exceptIds): array
    {
        return self::all()->where('chat_id', $chatId)
            ->where('is_deleted', false)
            ->where('user_id', $userId)
            ->whereNotIn('message_id', $exceptIds)
            ->toArray();
    }
}
