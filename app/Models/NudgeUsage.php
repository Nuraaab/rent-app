<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NudgeUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nudges_used',
        'nudges_purchased',
        'last_reset_date',
    ];

    protected $casts = [
        'last_reset_date' => 'date',
    ];

    /**
     * Get the user who owns this nudge usage record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the total available nudges.
     */
    public function getAvailableNudgesAttribute(): int
    {
        // 3 free nudges per month + purchased nudges
        return 3 + $this->nudges_purchased - $this->nudges_used;
    }

    /**
     * Check if the user can send a nudge.
     */
    public function canNudge(): bool
    {
        // Reset monthly if it's a new month
        if ($this->last_reset_date->month != now()->month || 
            $this->last_reset_date->year != now()->year) {
            $this->nudges_used = 0;
            $this->last_reset_date = now();
            $this->save();
        }

        return $this->nudges_used < (3 + $this->nudges_purchased);
    }

    /**
     * Increment nudge usage.
     */
    public function incrementUsage(): void
    {
        $this->increment('nudges_used');
    }
}
