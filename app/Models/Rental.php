<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $casts = [
        'price' => 'float',
        'latitude' => 'float',
        'longtiude' => 'float',
        'sqft' => 'float',
    ];

    protected $fillable=[
        'title',
        'category',
        'description',
        'category_id',
        'property_type',
        'listing_type',
        'rent_type',
        'max_number_of_gusts',
        'number_of_bedrooms',
        'number_of_beds',
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
      
      public function houseGallery(){
        return $this->hasMany(HouseGallary::class);
      }
      public function bedGallery(){
        return $this->hasMany(BedGallery::class);
      }
    public function review(){
    return $this->hasMany(Review::class);
      }

      public function reviews()
      {
        return $this->review();
      }

      public function house_rule(){
        return $this->hasMany(HouseRule::class);
      }

      public function houseRules()
      {
        return $this->house_rule();
      }

      public function house_offer(){
        return $this->hasMany(HouseOffer::class);
      }

      public function houseOffers()
      {
        return $this->house_offer();
      }

      public function favorites()
      {
        return $this->hasMany(Favorite::class);
      }
}
