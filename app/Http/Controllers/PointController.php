<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
class PointController extends Controller
{
    public function index(Request $request){
    	$points=Point::where('businessId',$request->json('businessId'))->get();
    	return response()->json(['status'=>200,'data'=>$points]);
    }
    public function store(Request $request){
    	if(empty($request->json('businessId'))){
    		return response()->json(['status'=>401,'message'=>'businessId is required']);
    	}
    	$point=new Point;
    	$point->points=$request->json('points');
    	$point->price=$request->json('price');
    	$point->businessId=$request->json('businessId');
        $point->save();
        return response()->json(['status'=>200,'message'=>'stored successfully']);
    }
    public function update(Request $request){
    	if(empty($request->json('pointId'))){
    		return response()->json(['status'=>401,'message'=>'pointId is required']);
    	}
    	$point=Point::find($request->json('pointId'));
    	if(!$point){
    		return response()->json(['status'=>401,'message'=>'point does not exists']);
    	}
    	$point->points=$request->json('points');
    	$point->price=$request->json('price');
        $category->save();
        return response()->json(['status'=>200,'message'=>'updated successfully']);
    }
    public function delete(Request $request){
    	if(empty($request->json('pointId'))){
    		return response()->json(['status'=>401,'message'=>'pointId is required']);
    	}
    	$point=Point::find($request->json('pointId'));
    	if(!$point){
    		return response()->json(['status'=>401,'message'=>'point does not exists']);
    	}
    	$point->delete();
    	return response()->json(['status'=>200,'message'=>'deleted successfully']);
    }
}
