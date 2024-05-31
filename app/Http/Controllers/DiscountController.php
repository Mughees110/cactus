<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
class DiscountController extends Controller
{
    public function index(Request $request){
    	$discounts=Discount::where('businessId',$request->json('businessId'))->get();
    	return response()->json(['status'=>200,'data'=>$discounts]);
    }
    public function store(Request $request){
    	if(empty($request->json('businessId'))){
    		return response()->json(['status'=>401,'message'=>'businessId is required']);
    	}
    	$discount=new Discount;
    	$discount->discount=$request->json('discount');
    	$discount->type=$request->json('type');
    	$discount->points=$request->json('points');
    	$discount->businessId=$request->json('businessId');
        $discount->save();
        return response()->json(['status'=>200,'message'=>'stored successfully']);
    }
    public function update(Request $request){
    	if(empty($request->json('discountId'))){
    		return response()->json(['status'=>401,'message'=>'discountId is required']);
    	}
    	$discount=Discount::find($request->json('discountId'));
    	if(!$discount){
    		return response()->json(['status'=>401,'message'=>'discount does not exists']);
    	}
    	$discount->discount=$request->json('discount');
    	$discount->type=$request->json('type');
    	$discount->points=$request->json('points');
        $discount->save();
        return response()->json(['status'=>200,'message'=>'updated successfully']);
    }
    public function delete(Request $request){
    	if(empty($request->json('discountId'))){
    		return response()->json(['status'=>401,'message'=>'discountId is required']);
    	}
    	$discount=Discount::find($request->json('discountId'));
    	if(!$discount){
    		return response()->json(['status'=>401,'message'=>'discount does not exists']);
    	}
    	$discount->delete();
    	return response()->json(['status'=>200,'message'=>'deleted successfully']);
    }
}
