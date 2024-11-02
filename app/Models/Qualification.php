<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;
    protected $fillable=[
        'qualification',
        'job_position_id'
    ];
    public function jobPosition(){ 
        return $this->belongsTo(JobPosition::class);
    }
}
