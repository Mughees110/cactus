<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Rating;
class AdminController extends Controller
{
    public function index(){
    	$ratings=Rating::all();
    	//DB::statement('ALTER TABLE users ADD fcm LONGTEXT;');
    	dd($ratings);
    }
}
