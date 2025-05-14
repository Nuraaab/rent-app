<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'category',
        'category_id',
        'job_salary',
        'job_type',
        'client',
        'deadline',
        'description',
        'phone_number',
        'address',
        'latitude',
        'longitude',
        'user_id'
    ];
    public function category(){
      return $this->belongsTo(Category::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
      }

      public function responsiblity(){
        return $this->hasMany(Responsiblity::class);
   }

      public function qualification(){
        return $this->hasMany(Qualification::class);
      }
}
