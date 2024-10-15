<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Rating;
use App\Models\Consumption;
use App\Models\Count;
class AdminController extends Controller
{
    public function index(){
    	$ratings=Rating::all();
    	//DB::statement('ALTER TABLE users ADD fcm LONGTEXT;');
    	dd($ratings);
    }
    public function getStats(Request $request){
    	if(empty($request->json('userId'))){
    		return response()->json(['status'=>401,'message'=>'userId required']);
    	}
    	$numOfCon=Consumption::where('userId',$request->json('userId'))->count();
    	$totalPrice=0;
    	$totalPoints=0;
    	$counts=Count::where('userId',$request->json('userId'))->get();
    	foreach ($counts as $key => $value) {
    		$totalPrice=$totalPrice+$value->price;
    		$totalPoints=$totalPoints+$value->points;
    	}
    	$consumeTotalPoints=0;
    	$cons=Consumption::where('userId',$request->json('userId'))->get();
    	foreach ($cons as $key => $value) {
    		$consumeTotalPoints=$consumeTotalPoints+$value->points;
    	}
    	return response()->json(['status'=>200,'numberOfConsumptions'=>$numOfCon,'totalPriceSpent'=>$totalPrice,'totalPointsGot'=>$totalPoints,'consumeTotalPoints'=>$consumeTotalPoints]);

    }
}
