<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseOffer extends Model
{
    use HasFactory;
    protected $fillable=[
        'offer_name',
        'rental_id'
    ];
    public function rental(){
        return $this->belongsTo(Rental::class);
      }
}
