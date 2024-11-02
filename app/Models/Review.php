<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable=[
        'rating',
        'comment',
        'rental_id',
        'user_id'
    ];
    public function rental(){
        return $this->belongsTo(Rental::class);
      }
      public function user(){
        return $this->belongsTo(User::class);
      }
}
