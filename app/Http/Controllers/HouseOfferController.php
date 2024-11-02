<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HouseOfferRequest;
use App\Http\Resources\HouseOfferResource;
use App\Models\HouseOffer;
class HouseOfferController extends Controller
{
    public function getHouseOffer(){
        $offer = HouseOffer::select('id', 'offer_name', 'rental_id')
        ->whereIn('id', function ($query) {
            $query->select(\DB::raw('MAX(id)'))
                  ->from('house_offers')
                  ->groupBy('offer_name');
        })
        ->orderBy('id', 'desc')
        ->get();
    
        $response=HouseOfferResource::collection($offer);
        return response($response,200);
    }
    public function addHouseOffer(HouseOfferRequest $request){
        $offer = HouseOffer::create([
              'offer_name' => $request->offer_name,
              'rental_id' => $request->rental_id
        ]);
        $response=[
          'message'=>'House Offer Created',
          'offer'=>$offer,
      ];
      return response($response,200);
      }

      public function updateHouseOffer(HouseOfferRequest $request, $id){
        $offer = HouseOffer::find($id);
        $offer->offer_name = $request->offer_name;
        $offer->rental_id = $request->rental_id;
        $offer->save();

        if($offer){
            $response =[
                'message'=> 'Offer Updated',
                'offer' => $offer
            ];
        }else{
            $response =[
                'message'=> 'Error on Updateding',
            ];
        }
        
        return response($response, 200);

      }

    public function deleteHouseOffer($id){
        $offer = HouseOffer::find($id);
        if ($offer) {
            $offer->delete();
            $response = [
                'message' => 'Offer deleted successfully!'
            ];
            return response($response, 200);
        } else {
            $response = [
                'message' => 'Offer not found!'
            ];
            return response($response, 404);
        }
    }
}
