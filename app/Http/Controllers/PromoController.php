<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\Models\Promo;
class PromoController extends Controller
{
    public function index(Request $request){
    	$points=Promo::all();
    	return response()->json(['status'=>200,'data'=>$points]);
    }
    public function store(Request $request){
    	
    	$promo=new Promo;
    	$promo->title=$request->get('title');
    	$promo->description=$request->get('description');
    	$promo->price=$request->get('price');
        $promo->businessId=$request->get('businessId');
    	$image=Input::file("image");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $promo->image=$picPath;
        }
        $promo->save();
        return response()->json(['status'=>200,'message'=>'stored successfully']);
    }
    public function update(Request $request){
    	if(empty($request->get('promoId'))){
    		return response()->json(['status'=>401,'message'=>'promoId is required']);
    	}
    	$promo=Promo::find($request->get('promoId'));
    	if(!$promo){
    		return response()->json(['status'=>401,'message'=>'promo does not exists']);
    	}
    	$promo->title=$request->get('title');
    	$promo->description=$request->get('description');
    	$promo->price=$request->get('price');
        if(!empty($request->json('businessId'))){
            $promo->businessId=$request->get('businessId');
        }
    	$image=Input::file("image");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $promo->image=$picPath;
        }
        $promo->save();
        return response()->json(['status'=>200,'message'=>'updated successfully']);
    }
    public function delete(Request $request){
    	if(empty($request->json('promoId'))){
    		return response()->json(['status'=>401,'message'=>'promoId is required']);
    	}
    	$promo=Promo::find($request->json('promoId'));
    	if(!$item){
    		return response()->json(['status'=>401,'message'=>'promo does not exists']);
    	}
    	$promo->delete();
    	return response()->json(['status'=>200,'message'=>'deleted successfully']);
    }
}
