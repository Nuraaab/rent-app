<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Http\Requests\RentalRequest;
use App\Http\Resources\RentalResource;
class RentalController extends Controller
{
    public function getRental(){
        $house=Rental::latest()->get();
        $response=RentalResource::collection($house);
        return response($response,200);
    }

    public function addRental(RentalRequest $request){
        $house = Rental::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'max_number_of_gusts' => $request->max_number_of_gusts,
            'number_of_bedrooms' => $request->number_of_bedrooms,
            'number_of_baths' => $request->number_of_baths,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longtiude' => $request->longtiude,
            'price' => $request->price,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => $request->user_id,
            ]);
    
            $response=[
                'message'=>'Rent House Created',
                'id' => $house->id,
                'house'=>$house,
            ];
            return response($response,200);
    }

    public function updateRental(RentalRequest $request, $id){
        $house = Rental::find($id);
        $house->title = $request->title;
        $house->description = $request->description;
        $house->category_id = $request->category_id;
        $house->max_number_of_gusts = $request->max_number_of_gusts;
        $house->number_of_bedrooms = $request->number_of_bedrooms;
        $house->number_of_baths = $request->number_of_baths;
        $house->phone_number = $request->phone_number;
        $house->address = $request->address;
        $house->latitude = $request->latitude;
        $house->longtiude = $request->longtiude;
        $house->price = $request->price;
        $house->start_date = $request->start_date;
        $house->end_date = $request->end_date;
        $house->check_in_time = $request->check_in_time;
        $house->check_out_time = $request->check_out_time;
        $house->user_id = $request->user_id;
        $house->save();
        if($house){
            $response =[
                'message'=> 'Rent House Updated',
                'house' => $house
            ];
        }else{
            $response =[
                'message'=> 'Error on Updateding',
            ];
        }
        
        return response($response, 200);
    }

    public function deleteHouse($id){
        $house = Rental::find($id);
        if ($house) {
            $house->delete();
            $response = [
                'message' => 'House deleted successfully!'
            ];
            return response($response, 200);
        } else {
            $response = [
                'message' => 'House not found!'
            ];
            return response($response, 404);
        }
    }

}
