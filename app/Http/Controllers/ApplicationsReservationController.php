<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationsReservation;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ApplicationsReservationController extends Controller
{
    // public function create(Request $request)
    // {
    //     $request->validate([
    //         'job_position_id' => 'nullable|exists:job_positions,id',
    //         'rental_id' => 'nullable|exists:rentals,id',
    //         'notes' => 'nullable|string',
    //         'cv' => 'nullable|string',
    //         'application_letter' => 'nullable',
    //         'github_link' => 'nullable|string',
    //         'linkedin_link' => 'nullable|string',
    //         'portfolio_link' => 'nullable|string'
    //     ]);

    //     $application = ApplicationsReservation::create([
    //         'user_id' => auth()->id(),
    //         'job_position_id' => $request->job_position_id,
    //         'rental_id' => $request->rental_id,
    //         'notes' => $request->notes,
    //         'applied_at' => now(),
    //     ]);

    //     return response()->json(['message' => 'Action recorded successfully', 'data' => $application]);
    // }

    public function create(Request $request)
        {
            $request->validate([
                'job_position_id' => 'nullable|exists:job_positions,id',
                'rental_id' => 'nullable|exists:rentals,id',
                'notes' => 'nullable|string',
                'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'application_letter' => 'nullable|string|required_if:job_position_id,!null',
                'github_link' => 'nullable|url',
                'linkedin_link' => 'nullable|url',
                'portfolio_link' => 'nullable|url'
            ]);
            
            $existingApplication = ApplicationsReservation::where('user_id', auth()->id())
            ->where(function($query) use ($request) {
                $query->where('job_position_id', $request->job_position_id)
                      ->orWhere('rental_id', $request->rental_id);
            })
            ->exists();
    
        if ($existingApplication) {
            return response()->json([
                'message' => 'The application already exist.',
            ], 400); 
        }
            $cvPath = null;
            if ($request->hasFile('cv')) { 
                $cv = $request->file('cv'); 
                $extension = $cv->getClientOriginalExtension(); 
                $filename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME); 
                $publicId = 'cvs/' . $filename;
                $cvPath = Cloudinary::upload($cv->getRealPath(),[
                    'folder' => 'cvs/', 
                ]
                )->getSecurePath();
            }
          

            // Create the application record
            $application = ApplicationsReservation::create([
                'user_id' => auth()->id(),
                'job_position_id' => $request->job_position_id,
                'rental_id' => $request->rental_id,
                'notes' => $request->notes,
                'cv' => $cvPath, // Save the Cloudinary URL
                'application_letter' => $request->application_letter,
                'github_link' => $request->github_link,
                'linkedin_link' => $request->linkedin_link,
                'portfolio_link' => $request->portfolio_link,
                'applied_at' => now(),
            ]);

            return response()->json([
                'message' => 'Action recorded successfully',
                'cv path' => $cvPath,
            ], 201);
        }

        public function cancelStatus($id) {
            $application = ApplicationsReservation::findOrFail($id);
        
            if (!empty($application->cv) && filter_var($application->cv, FILTER_VALIDATE_URL)) {
                $urlParts = parse_url($application->cv);
                $pathParts = explode('/', ltrim($urlParts['path'], '/'));
                
                // Extract correct public ID for Cloudinary
                if (count($pathParts) > 2) {
                    $publicId = implode('/', array_slice($pathParts, 2)); // Remove first two parts
                    $publicId = pathinfo($publicId, PATHINFO_FILENAME); // Remove file extension
        
                    $response = Cloudinary::destroy($publicId);
                    
                    if ($response['result'] !== 'ok' && $response['result'] !== 'not found') {
                        return response()->json(['message' => 'Failed to delete CV from Cloudinary', 'error' => $response], 500);
                    }
                }
            }
        
            $application->delete();
            return response()->json(['message' => 'Application deleted successfully']);
        }
        

        public function index() //myApplication
        {
            $applications = ApplicationsReservation::where('user_id', auth()->id())
                ->with(['user','jobPosition', 'rental'])
                ->get();
            return response()->json($applications);
        }
        
        public function appForMe() //inbox
        {
            $userId = auth()->id();
            $applications = ApplicationsReservation::with(['user', 'jobPosition', 'rental'])
                ->whereHas('jobPosition', function ($query) use ($userId) {
                    $query->where('user_id', $userId); 
                })
                ->orWhereHas('rental', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->where('user_id', '!=', $userId)
                ->get();

            return response()->json($applications);
        }

        public function updateStatus(Request $request, $id){
            $application = ApplicationsReservation::findOrFail($id);
            $application->status = $request->status;
            $application->save();
            return response()->json([
                'message' => 'Status updated successfully',
            ], 200);
        } 


        public function testUpload(Request $request){
            $request->validate([
                'file' => 'required|mimes:pdf|max:2048', // Max 2MB
            ]);
    
            // Upload to Cloudinary
            $uploadedFile = Cloudinary::uploadFile($request->file('file')->getRealPath(), [
                'resource_type' => 'auto',
                'folder' => 'pdf_uploads', // Cloudinary folder
            ]);
    
            // Get the secure URL
            $pdfUrl = $uploadedFile->getSecurePath();
    
            return response()->json([
                'message' => 'PDF uploaded successfully!',
                'pdf_url' => $pdfUrl,
            ], 200);
        }
        
}
