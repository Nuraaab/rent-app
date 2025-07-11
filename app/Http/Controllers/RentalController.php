<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\HouseGallary;
use App\Models\BedGallery;
use App\Models\HouseOffer;
use App\Models\Category;
use App\Http\Requests\RentalRequest;
use App\Http\Requests\HouseRequest;
use App\Http\Resources\RentalResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
class RentalController extends Controller
{
    public function getRental(){
        $house=Rental::latest()->get();
        $response=RentalResource::collection($house);
        return response($response,200);
    }

    public function addRental(RentalRequest $request){
        $house = Rental::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'max_number_of_gusts' => $request->max_number_of_gusts,
            'number_of_bedrooms' => $request->number_of_bedrooms,
            'number_of_baths' => $request->number_of_baths,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longtiude' => $request->longtiude,
            'price' => $request->price,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => $request->user_id,
            ]);
    
            $response=[
                'message'=>'Rent House Created',
                'id' => $house->id,
                'house'=>$house,
            ];
            return response($response,200);
    }


    public function postRental(HouseRequest $request){
        $house = Rental::create([
            'title' => $request->title,
            'category'=>$request->category,
            'description' => $request->description,
            'max_number_of_gusts' => $request->max_number_of_gusts,
            'number_of_bedrooms' => $request->number_of_bedrooms,
            'number_of_baths' => $request->number_of_baths,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longtiude' => $request->longtiude,
            'price' => $request->price,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => $request->user_id,
        ]);
    
        $rentalId = $house->id;

        if ($request->has('category')) {
            $existingCategory = Category::where('cat_type', 'House')
            ->where('cat_name', $request->category)
            ->first();

        if (!$existingCategory) {
            Category::create([
                'cat_name' =>  $request->category,
                'cat_type' => 'House'
            ]);
        }
        }
        if ($request->has('house_offers')) {
            $houseOffers = json_decode($request->house_offers, true); 
        
            if (is_array($houseOffers)) {
                foreach ($houseOffers as $offer) {
                    $existingOffer = HouseOffer::where('rental_id', $rentalId)
                        ->where('offer_name', $offer['offer_name'])
                        ->first();
        
                    if (!$existingOffer) {
                        HouseOffer::create([
                            'rental_id' => $rentalId,
                            'offer_name' => $offer['offer_name'],
                        ]);
                    }
                }
            }
        }

       
        
        
        $uploadedImages = [];
        if ($request->hasFile('images')) {  // Ensure images exist and are files
            foreach ($request->file('images') as $image) { 
                $uploadedFile = Cloudinary::upload($image->getRealPath())->getSecurePath();
                HouseGallary::create([
                    'rental_id' => $rentalId,
                    'gallery_path' => $uploadedFile,
                ]);
                $uploadedImages[] = $uploadedFile;
            }
        }

        $uploadedBedGalleries = [];
        if ($request->hasFile('bedimages')) {  // Ensure bedimages exist and are files
            foreach ($request->file('bedimages') as $image) {
                $uploadedBedImage = Cloudinary::upload($image->getRealPath())->getSecurePath();
                BedGallery::create([
                    'rental_id' => $rentalId,
                    'gallery_path' => $uploadedBedImage,
                ]);
                $uploadedBedGalleries[] = $uploadedBedImage;
            }
        }

        return response([
            'message' => 'Rent House Created Successfully',
            'id' => $rentalId,
            'house' => $house,
            'categories' => Category::where('cat_type', 'House')->get(),
            'house_offers' => $house->house_offer ?? [],
            'images' => $house->gallery ?? [],
        ], 200);
    }
    public function deleteGallery($id)
        {
        
            $gallery = HouseGallary::find($id);
            if (!$gallery) {
                return response()->json(['message' => 'Gallery not found'], 404);
            }
            if ($gallery->gallery_path) {
                $urlParts = parse_url($gallery->gallery_path);
                $pathParts = explode('/', $urlParts['path']);
                $publicId = pathinfo($pathParts[count($pathParts) - 1], PATHINFO_FILENAME);
                $response = Cloudinary::destroy($publicId);
                if ($response['result'] !== 'ok') {
                    return response()->json(['message' => 'Failed to delete image from Cloudinary', 'error' => $response], 500);
                }
            }
            $gallery->delete();
            return response()->json(['message' => 'Image deleted successfully']);
        }

        public function deleteBedGallery($id){
            $gallery = BedGallery::find($id);
            if (!$gallery) {
                return response()->json(['message' => 'Gallery not found'], 404);
            }
        
            if ($gallery->gallery_path) {
                // Extract the public ID from the URL
                $urlParts = parse_url($gallery->gallery_path);
                $pathParts = explode('/', $urlParts['path']);
                $publicId = pathinfo($pathParts[count($pathParts) - 1], PATHINFO_FILENAME);
        
                // Delete the image from Cloudinary
                $response = Cloudinary::destroy($publicId);
                
                if ($response['result'] !== 'ok') {
                    return response()->json(['message' => 'Failed to delete image from Cloudinary', 'error' => $response], 500);
                }
            }
        
            // Delete the gallery record from your database
            $gallery->delete();
            
            return response()->json(['message' => 'Image deleted successfully']);
        }
        
    public function updateGallery(Request $request, $id){
        $gallery = Rental::find($id);

        if($gallery){
            $rentalId = $gallery->id;
            $uploadedImages = [];
        if ($request->hasFile('images')) {  
            foreach ($request->file('images') as $image) { 
                $uploadedFile = Cloudinary::upload($image->getRealPath())->getSecurePath();
                HouseGallary::create([
                    'rental_id' => $rentalId,
                    'gallery_path' => $uploadedFile,
                ]);
                $uploadedImages[] = $uploadedFile;
            }
        }

        $uploadedBedGalleries = [];
        if ($request->hasFile('bedimages')) { 
            foreach ($request->file('bedimages') as $image) {
                $uploadedBedImage = Cloudinary::upload($image->getRealPath())->getSecurePath();
                BedGallery::create([
                    'rental_id' => $rentalId,
                    'gallery_path' => $uploadedBedImage,
                ]);
                $uploadedBedGalleries[] = $uploadedBedImage;
            }
        }
        return response([
            'message' => 'Rent House Created Successfully',
            'House Gallery' => $uploadedImages,
            'Bed Gallery' => $uploadedBedGalleries,
        ], 200);
        }else{
            return response([
                'message' => 'The house not found',
            ], 400); 
        }
    }

    public function updateRental(RentalRequest $request, $id)
    {
        $house = Rental::find($id);
        if (!$house) {
            return response(['message' => 'House not found'], 404);
        }
    
        // Update basic house details
        $house->title = $request->title;
        $house->description = $request->description;
        $house->category = $request->category;
        $house->max_number_of_gusts = $request->max_number_of_gusts;
        $house->number_of_bedrooms = $request->number_of_bedrooms;
        $house->number_of_baths = $request->number_of_baths;
        $house->phone_number = $request->phone_number;
        $house->address = $request->address;
        $house->latitude = $request->latitude;
        $house->longtiude = $request->longtiude;
        $house->price = $request->price;
        $house->start_date = $request->start_date;
        $house->end_date = $request->end_date;
        $house->check_in_time = $request->check_in_time;
        $house->check_out_time = $request->check_out_time;
        $house->user_id = $request->user_id;
        $house->save();

        
        if ($request->has('category')) {
            $existingCategory = Category::where('cat_type', 'House')
            ->where('cat_name', $request->category)
            ->first();

        if (!$existingCategory) {
            Category::create([
                'cat_name' =>  $request->category,
                'cat_type' => 'House'
            ]);
        }
        }
        if ($request->has('house_offer')) {
            $currentOffers = $house->house_offer->pluck('offer_name')->toArray();
            $offers = collect($request->house_offer);
            $newOffers = $offers->filter(function ($offer) use ($currentOffers) {
                return !in_array($offer['offer_name'], $currentOffers);
            });
            $house->house_offer()->whereNotIn('offer_name', $offers->pluck('offer_name'))->delete();
            if ($newOffers->isNotEmpty()) {
                        $offersToSave = $newOffers->map(function ($offer) {
                            return new HouseOffer(['offer_name' => $offer['offer_name']]);
                        });
                        $house->house_offer()->saveMany($offersToSave);
                    }
                }
    
        return response([
            'message' => 'Rent House Updated',
            'house' => $house
        ], 200);
    }
    

    public function deleteHouse($id){
        try{
            $house = Rental::findOrFail($id);
            if ($house) {
              
                foreach($house->gallery as $gall){
                    if ($gall->gallery_path) {
                        $urlParts = parse_url($gall->gallery_path);
                        $pathParts = explode('/', $urlParts['path']);
                        $publicId = pathinfo($pathParts[count($pathParts) - 1], PATHINFO_FILENAME);
                
                        $response = Cloudinary::destroy($publicId);
                        
                        if ($response['result'] !== 'ok') {
                            return response()->json(['message' => 'Failed to delete image from Cloudinary', 'error' => $response], 500);
                        }
                    }
                }
                $house->gallery()->delete();
                foreach($house->bedGallery as $bedGall){
                    if ($bedGall->gallery_path) {
                        $urlParts = parse_url($bedGall->gallery_path);
                        $pathParts = explode('/', $urlParts['path']);
                        $publicId = pathinfo($pathParts[count($pathParts) - 1], PATHINFO_FILENAME);
                
                        $response = Cloudinary::destroy($publicId);
                        
                        if ($response['result'] !== 'ok') {
                            return response()->json(['message' => 'Failed to delete image from Cloudinary', 'error' => $response], 500);
                        }
                    }
                }
                $house->bedGallery()->delete();
    
                $house->house_offer()->delete();
                $house->review()->delete();
                $house->delete();
                $response = [
                    'message' => 'House deleted successfully!'
                ];
                return response($response, 200);
            } else {
                $response = [
                    'message' => 'House not found!'
                ];
                return response($response, 404);
            }
        }catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete house', 'error' => $e->getMessage()], 500);
        }
       
    }

}
