<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use App\Models\User;
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
        $point->save();
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
    public function submitToCalculate(Request $request){
        if(empty($request->json('secret-key'))||empty($request->json('userId'))||empty($request->json('businessId'))||empty($request->json('price'))){
            return response()->json(['status'=>401,'message'=>'secret-key userId businessId price required']);
        }
        if($request->json('secret-key')!="base64:UtWHLdJ5muqnQ+E8p8jV/anRE24QQLy+FwFALtTtoM8="){
            return response()->json(['status'=>401,'message'=>'secret key does not match']);
        }
        $point=Point::where('businessId',$request->json('businessId'))->first();
        if(!$point){
            return response()->json(['status'=>401,'message'=>'Points record missing']);
        }
        $user=User::find($request->json('userId'));
        if(!$user){
            return response()->json(['status'=>401,'message'=>'user does not exists']);
        }
        $pp=$point->points/$point->price;
        $tp=$request->json('price')*$pp;
        $user->points=$tp;
        $user->save();
        return response()->json(['status'=>200,'data'=>$user,'message'=>'Added successfully']);

    }
}
