<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\QualificationRequest;
use App\Http\Resources\QualificationResource;
use App\Models\Qualification;

class QualificationController extends Controller
{
    public function getQualification($id){
        $qualification = Qualification::where('job_position_id', $id)->get();
        $response=QualificationResource::collection($qualification);
          
          return response($response,200);
    }
    public function addQualification(QualificationRequest $request){
        $qualification = Qualification::create([
            'qualification' => $request->qualification,
            'job_position_id' => $request->job_position_id,
            ]);
    
            $response=[
                'message'=>'Qualification Created',
                'jobs'=>$qualification,
            ];
            return response($response,200);
    }

    public function updateQualification(QualificationRequest $request, $id){
        $qualification = Qualification::find($id);
        $qualification->qualification = $request->qualification;
        $qualification->job_position_id = $request->job_position_id;
        $qualification->save();

        if($qualification){
            $response =[
                'message'=> 'Qualification Updated',
                'qualification' => $qualification
            ];
        }else{
            $response =[
                'message'=> 'Error on Updateding',
            ];
        }
        
        return response($response, 200);
    }

    public function deleteQualification($id){
        $qualification = Qualification::find($id);
        if ($qualification) {
            $qualification->delete();
            $response = [
                'message' => 'Qualification deleted successfully!'
            ];
            return response($response, 200);
        } else {
            $response = [
                'message' => 'Qualification not found!'
            ];
            return response($response, 404);
        }
    }
  

}

