<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_user_id',
        'type', // 'like' or 'nudge'
    ];

    /**
     * Get the user who gave the interaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who received the interaction.
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
