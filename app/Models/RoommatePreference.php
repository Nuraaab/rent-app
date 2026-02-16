<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoommatePreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender_preference',
        'min_budget',
        'max_budget',
        'preferred_locations',
        'lifestyle_preferences',
        'amenity_preferences',
        'smoking_preference',
        'pet_preference',
        'sleep_schedule',
        'move_in_from',
        'move_in_to',
    ];

    protected $casts = [
        'preferred_locations' => 'array',
        'lifestyle_preferences' => 'array',
        'amenity_preferences' => 'array',
        'move_in_from' => 'date',
        'move_in_to' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
