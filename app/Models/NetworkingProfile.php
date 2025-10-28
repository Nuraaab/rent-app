<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'cover_image',
        'privacy',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['is_connected', 'connection_count'];

    /**
     * Get the user who created this profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get connection requests for this profile.
     */
    public function connections()
    {
        return $this->hasMany(NetworkingConnection::class);
    }

    /**
     * Get accepted connections for this profile.
     */
    public function acceptedConnections()
    {
        return $this->hasMany(NetworkingConnection::class)->where('status', 'accepted');
    }

    /**
     * Check if the current user is connected to this profile.
     */
    public function getIsConnectedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->connections()
            ->where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Get the connection count.
     */
    public function getConnectionCountAttribute(): int
    {
        return $this->acceptedConnections()->count();
    }
}
