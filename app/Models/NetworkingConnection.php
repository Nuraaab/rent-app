<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkingConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'networking_profile_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who requested the connection.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the networking profile.
     */
    public function networkingProfile()
    {
        return $this->belongsTo(NetworkingProfile::class);
    }
}

