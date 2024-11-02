<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ResponsiblityRequest;
use App\Http\Resources\ResponsiblityResource;
use App\Models\Responsiblity;
class ResponsiblityController extends Controller
{
    public function getResponsiblity($id){
        $responsiblity = Responsiblity::where('job_position_id', $id)->get();
        $response=ResponsiblityResource::collection($responsiblity);
          
          return response($response,200);
    }
    public function addResponsiblity(ResponsiblityRequest $request){
        $responsiblity = Responsiblity::create([
            'responsiblity' => $request->responsiblity,
            'job_position_id' => $request->job_position_id,
            ]);
    
            $response=[
                'message'=>'Responsiblity Created',
                'jobs'=>$responsiblity,
            ];
            return response($response,200);
    }

    public function updateResponsibility(ResponsiblityRequest $request, $id){
        $responsibility = Responsiblity::find($id);
        $responsibility->responsiblity = $request->responsiblity;
        $responsibility->job_position_id = $request->job_position_id;
        $responsibility->save();

        if($responsibility){
            $response =[
                'message'=> 'Responsibility Updated',
                'responsibility' => $responsibility
            ];
        }else{
            $response =[
                'message'=> 'Error on Updateding',
            ];
        }
        
        return response($response, 200);

    }

    public function deleteResponsibility($id){
        $responsibility = Responsiblity::find($id);
        if ($responsibility) {
            $responsibility->delete();
            $response = [
                'message' => 'Responsibility deleted successfully!'
            ];
            return response($response, 200);
        } else {
            $response = [
                'message' => 'res$responsibility not found!'
            ];
            return response($response, 404);
        }
    }
 }
