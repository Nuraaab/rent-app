<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoommateInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_user_id',
        'type',
        'matched_at',
    ];

    protected $casts = [
        'matched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}

