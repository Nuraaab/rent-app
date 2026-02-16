<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoommateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'gender',
        'age',
        'occupation',
        'budget_min',
        'budget_max',
        'preferred_locations',
        'lifestyle_tags',
        'amenity_preferences',
        'city',
        'state',
        'country',
        'move_in_date',
        'is_smoker',
        'has_pets',
        'sleep_schedule',
        'cleanliness_level',
        'social_level',
        'is_active',
    ];

    protected $casts = [
        'preferred_locations' => 'array',
        'lifestyle_tags' => 'array',
        'amenity_preferences' => 'array',
        'move_in_date' => 'date',
        'is_smoker' => 'boolean',
        'has_pets' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

