<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
class AdminController extends Controller
{
    public function index(){
    	$user=new User;
    	$user->name="hello";
    	$user->email="heelo@gmail.com";
    	$user->password="123456";
    	$user->role="test";
    	$user->save();
    	dd('yes');
    	DB::statement('ALTER TABLE users ADD stripeToken LONGTEXT;');
    	DB::statement('ALTER TABLE users ADD stripeId LONGTEXT;');
    	dd('ok');
    }
}
