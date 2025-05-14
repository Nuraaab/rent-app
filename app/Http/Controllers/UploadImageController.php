<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Exception;
class UploadImageController extends Controller
{


  
    public function upload(Request $request)
    {
        $uploadedFile = $request->file('file');

        // Upload to Cloudinary
        $uploadResult = Cloudinary::upload($uploadedFile->getRealPath())->getSecurePath();

        return response()->json([
            'url' => $uploadResult,
            'message' => 'File uploaded successfully!'
        ]);
    }

  

    public function getImage($filename)
    {
        try {
            // Generate Cloudinary URL
            $imageUrl = Cloudinary::getUrl($filename);

            // Return the image as a redirect to Cloudinary
            return redirect()->to($imageUrl);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image not found'], 404);
        }
    }


    public function getMultipleImage(Request $request)
    {
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

    public function uploadSingleImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = rand() . '.' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);

            return response()->json($fileName, 200);
        }

        return response()->json(['message' => 'Invalid file upload'], 400);
    }

    public function uploadMultipleImage(Request $request)
    {
        $uploadedFiles = [];

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                $uploadResult = Cloudinary::upload($file->getRealPath())->getSecurePath();
            //     $fileName = rand() . '.' . $file->getClientOriginalName();
            //     $file->move(public_path('images'), $fileName);
                $uploadedFiles[] = $uploadResult;
            }
            return response()->json($uploadedFiles, 200);
        }

        return response()->json(['message' => 'Invalid file upload'], 400);
    }
}
