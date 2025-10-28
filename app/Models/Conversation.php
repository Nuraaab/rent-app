<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the first user in the conversation
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user in the conversation
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get all messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message in this conversation
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get the other user in the conversation (relative to given user)
     */
    public function getOtherUser($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user2;
        }
        return $this->user1;
    }

    /**
     * Check if a user is part of this conversation
     */
    public function hasUser($userId)
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }

    /**
     * Find or create a conversation between two users
     */
    public static function findOrCreateBetween($user1Id, $user2Id)
    {
        // Always order user IDs to ensure consistency
        $orderedIds = [min($user1Id, $user2Id), max($user1Id, $user2Id)];

        $conversation = self::where('user1_id', $orderedIds[0])
            ->where('user2_id', $orderedIds[1])
            ->first();

        if (!$conversation) {
            $conversation = self::create([
                'user1_id' => $orderedIds[0],
                'user2_id' => $orderedIds[1],
            ]);
        }

        return $conversation;
    }
}
