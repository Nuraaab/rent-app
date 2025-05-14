<?php

namespace App\Http\Resources;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
            'id' => $this->id,
            'title'=>$this->title,
            'house_gallery' =>$this->gallery->map(function ($gallery) {
              return new HouseGalleryResource($gallery);
                }),
            'bed_gallery' =>$this->bedGallery->map(function ($gallery) {
              return new BedGalleryResource($gallery);
                }),
            'review' =>$this->review->map(function ($review) {
              return new ReviewResource($review);
                }),
            'house_offer' =>$this->house_offer->map(function ($offer) {
              return new HouseOfferResource($offer);
                }),
            'user' => $this->user ? $this->user : null,
            'description' => $this->description,
            'category' => $this->category,
            'max_number_of_gusts' => $this->max_number_of_gusts,
            'number_of_bedrooms' => $this->number_of_bedrooms,
            'number_of_baths' => $this->number_of_baths,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longtiude' => $this->longtiude,
            'price' => $this->price,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'user_id' => $this->user->id,
            'user_name' => $this->user->first_name . ' ' . $this->user->last_name,
            'profile' => $this->user->profile_image_path,
           ];
    }
}
