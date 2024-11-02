<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsiblity extends Model
{
    use HasFactory;
    protected $fillable=[
        'responsiblity',
        'job_position_id'
    ];
    public function jobPosition(){ 
        return $this->belongsTo(JobPosition::class);
    }
}
