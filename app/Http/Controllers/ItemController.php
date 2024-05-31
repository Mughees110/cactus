<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\Models\Item;
class ItemController extends Controller
{
    public function index(Request $request){
    	$items=Item::where('businessId',$request->json('businessId'))->get();
    	return response()->json(['status'=>200,'data'=>$items]);
    }
    public function store(Request $request){
    	if(empty($request->get('businessId'))||empty($request->get('title'))){
    		return response()->json(['status'=>401,'message'=>'businessId title is required']);
    	}
    	$item=new Item;
    	$item->title=$request->get('title');
    	$item->description=$request->get('description');
    	$item->points=$request->get('points');
    	$item->businessId=$request->get('businessId');
    	$image=Input::file("image");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $item->image=$picPath;
        }
        $item->save();
        return response()->json(['status'=>200,'message'=>'stored successfully']);
    }
    public function update(Request $request){
    	if(empty($request->get('itemId'))){
    		return response()->json(['status'=>401,'message'=>'itemId is required']);
    	}
    	$item=Item::find($request->get('itemId'));
    	if(!$item){
    		return response()->json(['status'=>401,'message'=>'item does not exists']);
    	}
    	$item->title=$request->get('title');
    	$item->description=$request->get('description');
    	$item->points=$request->get('points');
    	$item->businessId=$request->get('businessId');
    	$image=Input::file("image");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $item->image=$picPath;
        }
        $item->save();
        return response()->json(['status'=>200,'message'=>'updated successfully']);
    }
    public function delete(Request $request){
    	if(empty($request->json('itemId'))){
    		return response()->json(['status'=>401,'message'=>'itemId is required']);
    	}
    	$item=Item::find($request->json('itemId'));
    	if(!$item){
    		return response()->json(['status'=>401,'message'=>'item does not exists']);
    	}
    	$item->delete();
    	return response()->json(['status'=>200,'message'=>'deleted successfully']);
    }
}
