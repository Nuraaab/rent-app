<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NudgeUsage extends Model
{
    use HasFactory;

    protected $table = 'nudge_usage';

    protected $fillable = [
        'user_id',
        'nudges_used',
        'nudges_purchased',
        'last_reset_date',
    ];

    protected $casts = [
        'last_reset_date' => 'date',
        'nudges_used' => 'integer',
        'nudges_purchased' => 'integer',
    ];

    /**
     * Get the user who owns this nudge usage record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the total available nudges (free + purchased - used).
     */
    public function getAvailableNudgesAttribute(): int
    {
        $this->resetIfNeeded();
        
        $freeNudges = 3; // 3 free nudges per month
        $totalAvailable = $freeNudges + $this->nudges_purchased - $this->nudges_used;
        
        return max(0, $totalAvailable);
    }

    /**
     * Check if user can nudge (has available nudges).
     */
    public function canNudge(): bool
    {
        return $this->available_nudges > 0;
    }

    /**
     * Increment nudge usage.
     */
    public function incrementUsage(): void
    {
        $this->resetIfNeeded();
        $this->nudges_used++;
        $this->save();
    }

    /**
     * Reset nudges if it's a new month.
     */
    private function resetIfNeeded(): void
    {
        $now = Carbon::now();
        $lastReset = Carbon::parse($this->last_reset_date);
        
        // Reset if it's a different month
        if ($now->year !== $lastReset->year || $now->month !== $lastReset->month) {
            $this->nudges_used = 0;
            $this->nudges_purchased = 0;
            $this->last_reset_date = $now->toDateString();
            $this->save();
        }
    }
}
