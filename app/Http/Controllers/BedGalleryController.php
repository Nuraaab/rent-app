<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BedGalleryRequest;
use App\Http\Resources\BedGalleryResource;
use App\Models\BedGallery;
class BedGalleryController extends Controller
{

    public function getBedGallery(){
        $bed=BedGallery::latest()->get();
        $response=BedGalleryResource::collection($bed);
        return response($response,200);
    }
    public function addBedGallery(BedGalleryRequest $request){
        $bedGallery = BedGallery::create([
              'gallery_path' => $request->gallery_path,
              'rental_id' => $request->rental_id,
        ]);
        $response=[
          'message'=>'Bed Gallery Created',
          'gallery'=>$bedGallery,
      ];
      return response($response,200);
      }
}
