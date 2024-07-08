<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Input;
use Mail;
class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        try {
            if(empty($request->json('name'))||empty($request->json('email'))||empty($request->json('password'))||empty($request->json('phone'))||empty($request->json('role'))){
                return response()->json(['status'=>401,'message'=>',name , email, role, phone and password are required']);
            }
            
            $exists=User::where('email',$request->json('email'))->exists();
            if($exists==true){
                return response()->json(['status'=>401,'message'=>'Email already exists']);
            }
            DB::beginTransaction();
            
            $user=new User;
            $user->name=$request->json('name');
            $user->email=$request->json('email');
            $user->password=Hash::make($request->json('password'));
            $user->phone=$request->json('phone');
            $user->role=$request->json('role');
            
            /*$image=Input::file("companyLogo");
            if(!empty($image)){
                $newFilename=$image->getClientOriginalName();
                $destinationPath='files';
                $image->move($destinationPath,$newFilename);
                $picPath='files/' . $newFilename;
                $user->companyLogo=encrypt($picPath);
            }*/
            $user->save();
            $token = $user->createToken('auth_token')->plainTextToken;
            
            DB::commit();

            return response()->json(['status'=>200,'token'=>$token,'data'=>$user,'message'=>'Registered successfully']);

        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());

            DB::rollBack();
            
            return response()->json([
                'message' => 'User registration failed'.$e->getMessage(),
            ], 422);
        }
    }

    public function login(Request $request)
    {
        try {
            if(empty($request->json('email'))||empty($request->json('password'))){
                return response()->json(['status'=>401,'message'=>'Email and password are required']);
            }
            
            $user = User::where('email', $request->json('email'))->first();

            if (!$user || !Hash::check($request->json('password'), $user->password)) {
                return response()->json([
                    'message' => 'The credentials are invalid',
                ], 422);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $businessE=Business::where('userId',$user->id)->exists();
            if($businessE==true){
            	$business=Business::where('userId',$user->id)->first();
            	$business->setAttribute('category',Category::find($business->categoryId));
            }
            if($businessE==false){
            	$business=null;
            }

            
            return response()->json(['status'=>200,'token'=>$token,'data'=>$user,'business'=>$business]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('User Login failed: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'message' => 'User login failed'.$e->getMessage(),
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function update(Request $request)
    {
        try {
            $exists=User::where('id',$request->get('userId'))->exists();
            if($exists==true){
                return response()->json(['status'=>401,'message'=>'User does not exists']);
            }
            $user=User::find($request->get('userId'))
            DB::beginTransaction();
            if(!empty($request->get('name'))){
                $user->name=$request->get('name');
            }
            if(!empty($request->get('phone'))){
                $user->phone=$request->get('phone');
            }
            if(!empty($request->get('latitude'))){
                $user->latitude=$request->get('latitude');
            }
            if(!empty($request->get('longitude'))){
                $user->longitude=$request->get('longitude');
            }
            $image=Input::file("image");
            if(!empty($image)){
                $newFilename=$image->getClientOriginalName();
                $destinationPath='files';
                $image->move($destinationPath,$newFilename);
                $picPath='files/' . $newFilename;
                $user->picture=encrypt($picPath);
            }
            $user->save();
            DB::commit();

            return response()->json(['status'=>200,'data'=>$user,'message'=>'Updated successfully']);

        } catch (\Exception $e) {
            Log::error('User updation failed: ' . $e->getMessage());

            DB::rollBack();
            
            return response()->json([
                'message' => 'User updation failed'.$e->getMessage(),
            ], 422);
        }
    }
    public function gmail(Request $request){
        if(empty($request->json('email'))){
            return response()->json(['status'=>401,'message'=>'email is required']);
        }
        $exists=User::where('email',$request->json('email'))->exists();
        if($exists==false){
            $user=new User;
            $user->name=$request->json('name');
            $user->email=$request->json('email');
            $user->role='gmail';
            $user->password="123456";
            
            $user->save();
            return response()->json(['status'=>200,'data'=>$user,'exists'=>'no']);
        }
        $user=User::where('email',$request->json('email'))->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['status'=>200,'data'=>$user,'exists'=>'yes','token'=>$token]);
    }

}