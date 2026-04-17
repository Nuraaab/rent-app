<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityInvite extends Model
{
    use HasFactory;
    protected $fillable = [
        'sender_id',
        'recipient_user_id',
        'type',
        'target_id',
        'contact_name',
        'contact_phone',
        'contact_email',
        'invite_token',
        'status',
    ];

    public function sender(): BelongsTo {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
