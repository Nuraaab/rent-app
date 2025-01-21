<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationsReservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'job_position_id',
        'rental_id',
        'status',
        'notes',
        'applied_at',
        'cv',
        'application_letter',
        'github_link',
        'linkedin_link',
        'portfolio_link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
