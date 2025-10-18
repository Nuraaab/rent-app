<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'category',
        'description',
        'category_id',
        'property_type',
        'listing_type',
        'max_number_of_gusts',
        'number_of_bedrooms',
        'number_of_baths',
        'is_furnished',
        'sqft',
        'phone_number',
        'address',
        'country',
        'street_address',
        'apt',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longtiude',
        'price',
        'check_in_time',
        'check_out_time',
        'start_date',
        'end_date',
        'user_id',
    ];
    public function user(){
        return $this->belongsTo(User::class);
      }

      public function category(){
        return $this->belongsTo(Category::class);
      }
      public function gallery(){
        return $this->hasMany(HouseGallary::class);
      }
      public function bedGallery(){
        return $this->hasMany(BedGallery::class);
      }
      public function review(){
        return $this->hasMany(Review::class);
      }
      public function house_rule(){
        return $this->hasMany(HouseRule::class);
      }
      public function house_offer(){
        return $this->hasMany(HouseOffer::class);
      }
}
