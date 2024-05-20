<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Category;
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
    public function index(Request $request){
        $categories=Category::all();
        foreach ($categories as $key => $value) {
            $bs=Business::where('categoryId',$value->id)->get();
            if($bs){
                foreach ($bs as $key => $value) {
                    $latitudeFrom=(float)$value->latitude;
                    $longitudeFrom=(float)$value->longitude;
                    $latitudeTo=(float)$request->json('latitude');
                    $longitudeTo=(float)$request->json('longitude');
                    if(!empty($latitudeFrom)&&!empty($latitudeTo)&&!empty($longitudeFrom)&&!empty($longitudeTo)){
                        $long1 = deg2rad($longitudeFrom);
                        $long2 = deg2rad($longitudeTo);
                        $lat1 = deg2rad($latitudeFrom);
                        $lat2 = deg2rad($latitudeTo);
                         
                       //Haversine Formula
                        $dlong = $long2 - $long1;
                        $dlati = $lat2 - $lat1;
                        $val = pow(sin($dlati/2),2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2),2);
                        $res = 2 * asin(sqrt($val));
                        $radius = 3958.756;
                        $result= ($res*$radius)*1.60934;
                        $value->setAttribute('distance',$result);
                    }
                }
                $value->setAttribute('businesses',$bs);
            }
            
        }
        return response()->json(['status'=>200,'data'=>$categories]);
    }
    
}
