<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Point;
use App\Models\User;
use App\Models\Consumption;
use App\Models\Item;
use App\Models\Business;
use App\Models\Count;
class PointController extends Controller
{
    public function index(Request $request){
    	$point=Point::where('businessId',$request->json('businessId'))->first();
    	return response()->json(['status'=>200,'data'=>$point]);
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
        $pp=$point->price/$point->points;
        $tp=$request->json('price')/$pp;
        
            $count=new Count;
            $count->userId=$user->id;
            $count->businessId=$request->json('businessId');
            $count->points=$tp;
            $count->price=$request->json('price');
            $count->save();
        
        
        return response()->json(['status'=>200,'message'=>'Added successfully']);

    }
    public function consume(Request $request){
        $consume=new Consumption;
        $consume->points=$request->json('points');
        $consume->userId=$request->json('userId');
        $consume->itemId=$request->json('itemId');
        $consume->businessId=$request->json('businessId');
        $consume->save();
        return response()->json(['status'=>200,'message'=>'Stored successfully']);
    }
    public function getConsumes(Request $request){
        $consumes=Consumption::where('userId',$request->json('userId'))->get();
        foreach ($consumes as $key => $value) {
            $value->setAttribute('item',Item::find($value->itemId));
            $value->setAttribute('business',Business::find($value->businessId));
        }
        return response()->json(['status'=>200,'data'=>$consumes]);
    }
    public function getUserPoints(Request $request){
        $count=Count::where('userId',$request->json('userId'))->where('businessId',$request->json('businessId'))->get();
        return response()->json(['status'=>200,'data'=>$count]);
    }
    public function getUserAvailablePoints(Request $request){
        $got=0;
        $count=Count::where('userId',$request->json('userId'))->where('businessId',$request->json('businessId'))->get();
        foreach ($count as $key => $value) {
            $got=$got+$value->points;
        }
        $consumes=Consumption::where('userId',$request->json('userId'))->where('businessId',$request->json('businessId'))->get();
        $con=0;
        foreach ($consumes as $key => $value2) {
            $con=$con+$value2->points;
        }
        $avail=$coun-$con;
        return response()->json(['status'=>200,'data'=>$avail]);

    }
    public function purchasesAgainstBusiness(Request $request){
        if(empty($request->json('businessId'))){
            return response()->json(['status'=>'businessId required']);
        }
        $totalCount=Consumption::where('businessId',$request->json('businessId'))->count();
        $users=User::all();
        $result=array();
        foreach ($users as $key => $user) {
            $single=array();
            $single['user']=$user;
            $single['count']=Consumption::where('businessId',$request->json('businessId'))->where('userId',$user->id)->count();
            $records=Consumption::where('businessId',$request->json('businessId'))->where('userId',$user->id)->get();
            foreach ($records as $key => $value) {
                $value->setAttribute('item',Item::find($value->itemId));
            }
            $single['records']=$records;
            array_push($result,$single);
        }
        return response()->json(['status'=>200,'userWise'=>$result,'totalCount'=>$totalCount]);
    }
    public function countsAgainstBusiness(Request $request){
        if(empty($request->json('businessId'))){
            return response()->json(['status'=>'businessId required']);
        }
        $totalCount=0;
        $recs=Count::where('businessId',$request->json('businessId'))->get();
        foreach ($recs as $key => $value) {
            $totalCount=$totalCount+$value->points;
        }
        $totalCount2=0;
        foreach ($recs as $key => $value) {
            $totalCount2=$totalCount2+$value->price;
        }
        $users=User::all();
        $result=array();
        foreach ($users as $key => $user) {
            $single=array();
            $single['user']=$user;
            $userCount=0;
            $rs=Count::where('businessId',$request->json('businessId'))->where('userId',$user->id)->get();
            foreach ($rs as $key => $r) {
                $userCount=$userCount+$r->points;
            }
            $single['count']=$userCount;
            $records=Count::where('businessId',$request->json('businessId'))->where('userId',$user->id)->get();
            
            $single['records']=$records;
            
            array_push($result,$single);
            
        }
        return response()->json(['status'=>200,'userWise'=>$result,'totalCountPoints'=>$totalCount,'totalCountPrice'=>$totalCount2]);
    }
    public function countsAgainstBusiness2(Request $request){
        if(empty($request->json('businessId'))){
            return response()->json(['status'=>'businessId required']);
        }
        $totalCount=0;
        $recs=Count::where('businessId',$request->json('businessId'))->whereBetween('created_at',[$request->json('from'),$request->json('to')])->orderBy('created_at','desc')->get();
        foreach ($recs as $key => $value) {
            $totalCount=$totalCount+$value->points;
        }
        $totalCount2=0;
        foreach ($recs as $key => $value) {
            $totalCount2=$totalCount2+$value->price;
        }
        return response()->json(['status'=>200,'totalCountPoints'=>$totalCount,'totalCountPrice'=>$totalCount2,'data'=>$recs]);
    }
    public function consumesAgainstBusiness2(Request $request){
        if(empty($request->json('businessId'))){
            return response()->json(['status'=>'businessId required']);
        }
        $totalCount=0;
        $recs=Consumption::where('businessId',$request->json('businessId'))->whereBetween('created_at',[$request->json('from'),$request->json('to')])->orderBy('created_at','desc')->get();
        
        foreach ($recs as $key => $value) {
            $value->setAttribute('item',Item::find($value->itemId));
            $totalCount=$totalCount+$value->points;
        }
        return response()->json(['status'=>200,'totalCountPoints'=>$totalCount,'data'=>$recs]);
    }
    public function consumesAgainstBusiness(Request $request){
        if(empty($request->json('businessId'))){
            return response()->json(['status'=>'businessId required']);
        }
        $totalCount=0;
        $recs=Consumption::where('businessId',$request->json('businessId'))->get();
        foreach ($recs as $key => $value) {
            $totalCount=$totalCount+$value->points;
        }
        $items=Item::where('businessId',$request->json('businessId'))->get();
        foreach ($items as $key => $valuet) {
            $valuet->setAttribute('count',Consumption::where('itemId',$valuet->id)->where('businessId',$request->json('businessId'))->count());
        }
        $users=User::all();
        $result=array();
        foreach ($users as $key => $user) {
            $single=array();
            $single['user']=$user;
            $records=Consumption::where('businessId',$request->json('businessId'))->where('userId',$user->id)->get();
            $single['records']=$records;
            array_push($result,$single);
        }
        return response()->json(['status'=>200,'itemWise'=>$items,'totalPointConsumption'=>$totalCount,'userWise'=>$result]);
    }

}
