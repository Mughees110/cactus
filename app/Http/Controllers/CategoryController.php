<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Input;
class CategoryController extends Controller
{
    public function index(){
    	$categories=Category::all();
    	return response()->json(['status'=>200,'data'=>$categories]);
    }
    public function store(Request $request){
    	if(empty($request->get('name'))){
    		return response()->json(['status'=>401,'message'=>'name is required'])
    	}
    	$category=new Category;
    	$category->name=$request->get('name');
    	$image=Input::file("image");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $category->picture=$picPath;
        }
        $category->save();
        return response()->json(['status'=>200,'message'=>'stored successfully']);
    }
    public function update(Request $request){
    	if(empty($request->get('categoryId'))||empty($request->get('name'))){
    		return response()->json(['status'=>401,'message'=>'categoryId,name is required']);
    	}
    	$category=Category::find($request->get('categoryId'));
    	if(!$category){
    		return response()->json(['status'=>401,'message'=>'category does not exists']);
    	}
    	$category->name=$request->get('name');
    	$image=Input::file("image");
        if(!empty($image)){
            $newFilename=$image->getClientOriginalName();
            $destinationPath='files';
            $image->move($destinationPath,$newFilename);
            $picPath='files/' . $newFilename;
            $category->picture=$picPath;
        }
        $category->save();
        return response()->json(['status'=>200,'message'=>'updated successfully']);
    }
    public function delete(Request $request){
    	if(empty($request->json('categoryId'))){
    		return response()->json(['status'=>401,'message'=>'categoryId is required']);
    	}
    	$category=Category::find($request->json('categoryId'));
    	if(!$category){
    		return response()->json(['status'=>401,'message'=>'category does not exists']);
    	}
    	$category->delete();
    	return response()->json(['status'=>200,'message'=>'deleted successfully']);
    }
}
