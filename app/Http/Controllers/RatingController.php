<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
class RatingController extends Controller
{
    public function rating(Request $request){
    	$rating=new Rating;
    	$rating->stars=$request->json('stars');
    	$rating->comment=$request->json('comment');
    	$rating->businessId=$request->json('businessId');
    	$rating->userId=$request->json('userId');
    	return response()->json(['status'=>200,'message'=>'rated successfully']);
    }
}
