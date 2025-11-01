<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosition;
use App\Models\Responsiblity;
use App\Models\Qualification;
use App\Models\Category;
use App\Http\Requests\JobPositionRequest;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobPositionResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class JobPositionController extends Controller
{

    public function getJobs(){
        $jobs = JobPosition::latest()->get();
        $response=JobPositionResource::collection($jobs);
          
          return response($response,200);
    }
 
    public function addJob(JobRequest $request){
        $jobs = JobPosition::create([
            'title' => $request->title,
            'job_salary' => $request->job_salary,
            'job_type' => $request->job_type,
            'client' => $request->client,
            'deadline' => $request->deadline,
            'description' => $request->description,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'latitude' =>$request->latitude,
            'longitude' => $request->longitude,
            'category' => $request->category,
            'user_id' => $request->user_id,
            ]);

            $jobId = $jobs->id;

            if ($request->has('category')) {
                $existingCategory = Category::where('cat_type', 'Job')
                ->where('cat_name', $request->category)
                ->first();
    
            if (!$existingCategory) {
                Category::create([
                    'cat_name' =>  $request->category,
                    'cat_type' => 'Job'
                ]);
            }
            }
             // Handle Job Responsibilities
            if ($request->has('job_responsibility')) {
                foreach ($request->job_responsibility as $resp) {
                    Responsiblity::create([
                        'job_position_id' => $jobId,
                        'responsiblity' => $resp['responsiblity'], 
                    ]);
                }
            }

            // Handle Job Qualifications
            if ($request->has('job_qualification')) {
                foreach ($request->job_qualification as $qual) {
                    Qualification::create([
                        'job_position_id' => $jobId,
                        'qualification' => $qual['qualification'], 
                    ]);
                }
            }
            $response=[
                'message'=>'Job Created',
                'id' => $jobs->id,
                'jobs'=>$jobs,
            ];
            return response($response,200);
    }

    public function postJob(JobRequest $request){
        $jobs = JobPosition::create([
            'title' => $request->title,
            'job_salary' => $request->job_salary,
            'job_type' => $request->job_type,
            'client' => $request->client,
            'deadline' => $request->deadline,
            'description' => $request->description,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'latitude' =>$request->latitude,
            'longitude' => $request->longitude,
            'category_id' => $request->category_id,
            'user_id' => $request->user_id,
            // Company fields
            'company_name' => $request->company_name,
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'website' => $request->website,
            // Role fields
            'employment_type' => $request->employment_type,
            'seniority' => $request->seniority,
            // Location fields
            'work_modality' => $request->work_modality,
            'working_days' => $request->working_days,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'weekend_work' => $request->weekend_work ?? false,
            // Compensation fields
            'pay_type' => $request->pay_type,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'pay_cadence' => $request->pay_cadence,
            'equity' => $request->equity,
            'benefits' => $request->benefits, // JSON string
            // Requirements fields
            'experience' => $request->experience,
            'must_have_skills' => $request->must_have_skills, // JSON string
            'auth_required' => $request->auth_required ?? false,
            // Screening fields
            'resume_required' => $request->resume_required ?? true,
            'quick_apply' => $request->quick_apply ?? false,
            ]);

            $jobId = $jobs->id;
             // Handle Job Responsibilities
            if ($request->has('job_responsibility')) {
                foreach ($request->job_responsibility as $resp) {
                    Responsiblity::create([
                        'job_position_id' => $jobId,
                        'responsiblity' => $resp['responsiblity'], 
                    ]);
                }
            }

            // Handle Job Qualifications
            if ($request->has('job_qualification')) {
                foreach ($request->job_qualification as $qual) {
                    Qualification::create([
                        'job_position_id' => $jobId,
                        'qualification' => $qual['qualification'], 
                    ]);
                }
            }
            $response=[
                'message'=>'Job Created',
                'id' => $jobs->id,
                'jobs'=>$jobs,
            ];
            return response($response,200);
    }

    public function updateJob(JobRequest $request, $id){
        $job = JobPosition::find($id);
        $job->title = $request->title;
        $job->job_salary = $request->job_salary;
        $job->job_type = $request->job_type;
        $job->client = $request->client;
        $job->deadline = $request->deadline;
        $job->description = $request->description;
        $job->phone_number = $request->phone_number;
        $job->address=$request->address;
        $job->latitude =$request->latitude;
        $job->longitude= $request->longitude;
        $job->category = $request->category;
        $job->user_id = $request->user_id;
        $job->save();
        $jobId = $job->id;

        if ($request->has('category')) {
            $existingCategory = Category::where('cat_type', 'Job')
            ->where('cat_name', $request->category)
            ->first();

        if (!$existingCategory) {
            Category::create([
                'cat_name' =>  $request->category,
                'cat_type' => 'Job'
            ]);
        }
        }
            // Update Responsibilities using updateOrCreate (more efficient)
        if ($request->has('job_responsibility')) {
            $job->responsiblity()->delete(); // Clear old responsibilities
            $responsibilities = collect($request->job_responsibility)->map(function ($resp) {
                return new Responsiblity(['responsiblity' => $resp['responsiblity']]);
            });
            $job->responsiblity()->saveMany($responsibilities);
        }

        // Update Qualifications similarly
        if ($request->has('job_qualification')) {
            $job->qualification()->delete(); // Clear old qualifications
            $qualifications = collect($request->job_qualification)->map(function ($qual) {
                return new Qualification(['qualification' => $qual['qualification']]);
            });
            $job->qualification()->saveMany($qualifications);
        }

        return response([
            'message' => 'Job Updated Successfully',
        ], 200);
        }

        public function deleteJob($id){
           try{
            $job = JobPosition::findOrFail($id);
            if ($job) {
                $job->responsiblity()->delete();
                $job->qualification()->delete();
                $job->delete();
                $response = [
                    'message' => 'Job deleted successfully!'
                ];
                return response($response, 200);
            } else {
                $response = [
                    'message' => 'Job not found!'
                ];
                return response($response, 404);
            }
           }catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete job', 'error' => $e->getMessage()], 500);
        }
        }

    /**
     * Get user's own jobs.
     */
    public function myJobs(): JsonResponse
    {
        try {
            $userId = Auth::id();
            \Log::info('🔍 [MyJobs] User ID: ' . $userId);
            
            if (!$userId) {
                \Log::warning('🔴 [MyJobs] User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'data' => []
                ], 401);
            }
            
            $jobs = JobPosition::where('user_id', $userId)
                ->latest()
                ->get();
            
            \Log::info('🔍 [MyJobs] Found ' . $jobs->count() . ' jobs for user ' . $userId);
            
            if ($jobs->count() > 0) {
                \Log::info('🔍 [MyJobs] First job ID: ' . $jobs->first()->id);
                \Log::info('🔍 [MyJobs] First job title: ' . $jobs->first()->title);
            }
            
            $response = JobPositionResource::collection($jobs);
            
            \Log::info('🔍 [MyJobs] Response collection count: ' . count($response));

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            \Log::error('🔴 [MyJobs] Error: ' . $e->getMessage());
            \Log::error('🔴 [MyJobs] Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch your jobs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
