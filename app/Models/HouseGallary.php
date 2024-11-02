<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseGallary extends Model
{
    use HasFactory;
    protected $fillable=[
        'gallery_path',
        'rental_id'
    ];
    public function rental(){
        return $this->belongsTo(Rental::class);
      }
}
