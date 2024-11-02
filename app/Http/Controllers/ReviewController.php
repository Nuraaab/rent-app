<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
class ReviewController extends Controller
{
    public function getReview(){
        $review=Review::latest()->get();
        $response=ReviewResource::collection($review);
        return response($response,200);
    }
    public function addReview(ReviewRequest $request){
        $review = Review::create([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'rental_id' => $request->rental_id,
            'user_id' => $request->user_id,
            ]);
    
            $response=[
                'message'=>'Review Created',
                'jobs'=>$review,
            ];
            return response($response,200);
    }
}
