<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadImageController extends Controller
{

    public function getImage( $filename){
        $path = public_path('images/' . $filename);

        if (file_exists($path)) {
            return response()->file($path);
        }
    
        abort(404);
    }

    public function getMultipleImage(Request $request) {
        $filenames = $request->input('filenames');
        $images = [];
    
        foreach ($filenames as $filename) {
            $path = public_path('images/' . $filename);
    
            if (file_exists($path)) {
                $imageData = base64_encode(file_get_contents($path));
                $images[] = [
                    'filename' => $filename,
                    'image' => $imageData,
                ];
            } else {
                return response()->json(['message' => "File $filename not found"], 404);
            }
        }
    
        return response()->json($images, 200);
    }

    public function uploadSingleImage(Request $request){
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = rand().'.'.$file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);

            return response()->json($fileName, 200);
        }

        return response()->json(['message' => 'Invalid file upload'], 400);


    }

    public function uploadMultipleImage(Request $request){
        $uploadedFiles = [];
        
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                $fileName = rand().'.'.$file->getClientOriginalName();
                $file->move(public_path('images'), $fileName);
                $uploadedFiles[] = $fileName;
            }
            return response()->json($uploadedFiles, 200);
        }
    
        return response()->json(['message' => 'Invalid file upload'], 400);
    }
}
