<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'rental_id',
        'job_position_id',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

}
