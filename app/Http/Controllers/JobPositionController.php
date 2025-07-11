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
}
