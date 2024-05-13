<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use Input;
class BusinessController extends Controller
{

    public function update(Request $request){
    	if(empty($request->get('userId'))){
    		return response()->json(['status'=>401,'message'=>'userId is required']);
    	}
    	$businessE=Business::where('userId',$request->get('userId'))->exists();
    	if(!$businessE){
    		$business=new Business;
    		$business->userId=$request->get('userId');
    	}
    	if($businessE){
    		$business=Business::where('userId',$request->get('userId'))->first();
    	}
    	$business->name=$request->get('name');
    	$image=Input::file("image1");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $business->image1=$picPath;
        }
        $image=Input::file("image2");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $business->image2=$picPath;
        }
        $business->categoryId=$request->get('categoryId');
        $business->province=$request->get('province');
        $business->muncipality=$request->get('muncipality');
        $business->address=$request->get('address');
        $business->latitude=$request->get('latitude');
        $business->longitude=$request->get('longitude');
        $business->save();
        return response()->json(['status'=>200,'message'=>'updated successfully','data'=>$business]);
    }
    
}
