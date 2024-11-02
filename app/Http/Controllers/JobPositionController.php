<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosition;
use App\Http\Requests\JobPositionRequest;
use App\Http\Resources\JobPositionResource;
class JobPositionController extends Controller
{

    public function getJobs(){
        $jobs = JobPosition::latest()->get();
        $response=JobPositionResource::collection($jobs);
          
          return response($response,200);
    }
 
    public function addJob(JobPositionRequest $request){
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
            $response=[
                'message'=>'Job Created',
                'id' => $jobs->id,
                'jobs'=>$jobs,
            ];
            return response($response,200);
    }

    public function updateJob(JobPositionRequest $request, $id){
        $job = JobPosition::find($id);
        $job->title = $request->title;
        $job->category_id = $request->category_id;
        $job->job_salary = $request->job_salary;
        $job->job_type = $request->job_type;
        $job->client = $request->client;
        $job->deadline = $request->deadline;
        $job->description = $request->description;
        $job->phone_number = $request->phone_number;
        $job->address=$request->address;
        $job->latitude =$request->latitude;
        $job->longitude= $request->longitude;
        $job->user_id = $request->user_id;
        $job->save();
        if($job){  
            $response =[
                'message'=> 'Job Updated',
                'jobs' => $job
            ];
        }else{
            $response =[
                'message'=> 'Error on Updateding',
            ];
        }
        
        return response($response, 200);
        }

        public function deleteJob($id){
            $job = JobPosition::find($id);
            if ($job) {
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
        }
}
