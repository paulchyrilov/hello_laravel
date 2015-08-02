<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    public static function getChatHistory($senderId, $recipientId)
    {
        return self::where('sender_id', $senderId)->where('recipient_id', $recipientId)
            ->orWhere('sender_id', $recipientId)->where('recipient_id', $senderId)->get();
    }
}
