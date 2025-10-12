<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HouseGalleryRequest;
use App\Models\HouseGallary;
use App\Http\Resources\HouseGalleryResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HouseGallaryController extends Controller
{
  public function getHouseGallery(){
    $house=HouseGallary::latest()->get();
    $response=HouseGalleryResource::collection($house);
    return response($response,200);
}

// public function addHouseGallery(HouseGalleryRequest $request)
// {
//     $galleries = [];
//     $validated = $request->validated();
//     if ($request->hasFile('image')) {
//         $files = $request->file('image');
//         foreach ($files as $file) {
//             $fileName = rand() . '.' . $file->getClientOriginalExtension();
//             $file->move(public_path('images'), $fileName);
//             $houseGallery = HouseGallery::create([
//                 'gallery_path' => $fileName,
//                 'rental_id' => $request->rental_id,
//             ]);
//             $galleries[] = $houseGallery;
//         }

//         $response = [
//             'message' => 'House Galleries Created',
//             'galleries' => $galleries,
//         ];

//         return response($response, 200);
//     } else {
//         return response(['message' => 'No files uploaded'], 400);
//     }
// }

public function uploadImage(Request $request){
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        
        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(20) . '.' . $extension;
        
        // Create directories if they don't exist
        $uploadPath = public_path('assets/uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Move file to public/assets/uploads
        $file->move($uploadPath, $fileName);
        
        // Return the path relative to public
        $imagePath = '/assets/uploads/' . $fileName;
        
        return response()->json($imagePath, 200);
    }

    return response()->json(['message' => 'Invalid file upload'], 400);
}




   public function addHouseGallery(HouseGalleryRequest $request){
      $houseGallery = HouseGallary::create([
            'gallery_path' => $request->gallery_path,
            'rental_id' => $request->rental_id,
      ]);
      $response=[
        'message'=>'House Gallery Created',
        'gallery'=>$houseGallery,
    ];
    return response($response,200);
    }
}
