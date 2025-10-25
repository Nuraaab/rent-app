<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoommatePreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'budget_min',
        'budget_max',
        'gender_preference',
        'location_preference',
        'lifestyle_tags',
        'amenities',
        'about',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'lifestyle_tags' => 'array',
        'amenities' => 'array',
    ];

    /**
     * Get the user who owns this preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
