<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\User;
use App\Models\Category;
use App\Models\Count;
use App\Models\Consumption;
use App\Models\Rating;
use Input;
use Mail;

use App\Services\FirebaseService;
class BusinessController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
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
            $newFilename=time().$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $business->image1=$picPath;
        }
        $image2=Input::file("image2");
        if(!empty($image2)){
            $newFilename2=time().$image2->getClientOriginalName();
            $destinationPath2='files';
            $image2->move($destinationPath2,$newFilename2);
            $picPath2='files/' . $newFilename2;
            $business->image2=$picPath2;
        }
        $business->categoryId=$request->get('categoryId');
        $business->province=$request->get('province');
        $business->muncipality=$request->get('muncipality');
        $business->address=$request->get('address');
        $business->latitude=$request->get('latitude');
        $business->longitude=$request->get('longitude');
        $business->insta=$request->get('insta');
        $business->status="pending";
        $business->save();

        /*$user=User::find($request->get('userId'));
        if($user && $user->fcm){
            $fcmToken=$user->fcm;
            $deviceToken = $fcmToken;
            $title = 'Cactus';
            
            $result=array();
            $result['business']=$business;
            
            $data = $result;

            // Send notification and capture the response
            $response = $this->firebaseService->sendNotification($deviceToken, $title, $body, $data);
        }*/
        return response()->json(['status'=>200,'message'=>'updated successfully','data'=>$business]);
    }
    public function noti(Request $request){
        $user=User::find($request->json('userId'));
        if($user && $user->fcm){
            $fcmToken=$user->fcm;
            $deviceToken = $fcmToken;
            $title = 'Cactus';
            
            $result=array();
            $result['message']='Points added successfully';
            
            $data = $result;

            // Send notification and capture the response
            $response = $this->firebaseService->sendNotification($deviceToken, $title, $body, $data);
        }
        return response()->json(['status'=>200,'data'=>'sent']);
    }
    public function index(Request $request){
        $categories=Category::all();
        foreach ($categories as $key => $valueC) {
            $bs=Business::where('categoryId',$valueC->id)->get();
            
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
                    if(!empty($request->json('userId'))){
                        $csum=Count::where('businessId',$value->id)->where('userId',$request->json('userId'))->get();
                        $countPoints=0;
                        foreach ($csum as $keycs => $valuecs) {
                            $countPoints=$countPoints+$valuecs->points;
                        }
                        $cosum=Consumption::where('businessId',$value->id)->where('userId',$request->json('userId'))->get();
                        $countConsumes=0;
                        foreach ($cosum as $keycos => $valuecos) {
                            $countConsumes=$countConsumes+$valuecos->points;
                        }
                        $value->setAttribute('pointsGiven',$countPoints);
                        $value->setAttribute('pointsConsumed',$countConsumes);
                    }

                    $ratings=Rating::where('businessId',$value->id)->get();
                    foreach ($ratings as $key => $val) {
                        $value->setAttribute('user',User::find($val->userId));
                    }
                    $value->setAttribute('ratings',$ratings);
                }
                $valueC->setAttribute('businesses',$bs);
            
        }
        return response()->json(['status'=>200,'data'=>$categories]);
    }
    public function index2(Request $request){
        $bs=Business::all();
            
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
            $cat=Category::find($value->categoryId);
            $value->setAttribute('category',$cat);
            $user=User::find($value->userId);
            if($user){
                $value->setAttribute('user',$user);
            }
            $csum=Count::where('businessId',$value->id)->get();
            $countPoints=0;
            foreach ($csum as $keycs => $valuecs) {
                $countPoints=$countPoints+$valuecs->points;
            }
            $value->setAttribute('pointsGiven',$countPoints);
            $ratings=Rating::where('businessId',$value->id)->get();
            foreach ($ratings as $key => $val) {
                $value->setAttribute('user',User::find($val->userId));
            }
            $value->setAttribute('ratings',$ratings);
        }
        return response()->json(['status'=>200,'data'=>$bs]);
    }
    public function businessApprove(Request $request){
        $business=Business::find($request->json('businessId'));
        $business->status="approved";
        $business->save();
        $user=User::find($business->userId);
        if($user){
            $email=$user->email;
            
            Mail::send('mail2',[], function($message) use($email){
                     $message->to($email)->subject('¡Aprobado po');
                     $message->from('carlos@cacturaconcactus.com');
                    });
        }
        return response()->json(['status'=>200,'message'=>'approved successfully']);
    }
    public function businessReject(Request $request){
        $business=Business::find($request->json('businessId'));
        $business->status="rejected";
        $business->save();
        return response()->json(['status'=>200,'message'=>'rejected successfully']);
    }
    public function getRating(Request $request){
        $ratings=Rating::where('businessId',$request->json('businessId'))->get();
        foreach ($ratings as $key => $value) {
            $value->setAttribute('user',User::find($value->userId));
        }
        return response()->json(['status'=>200,'data'=>$ratings]);
    }
    
}
