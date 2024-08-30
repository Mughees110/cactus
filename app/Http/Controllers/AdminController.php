<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
class AdminController extends Controller
{
    public function index(){
    	
    	DB::statement('ALTER TABLE businesses ADD insta VARCHAR(255);');
    	dd('ok');
    	DB::statement('ALTER TABLE users ADD stripeId LONGTEXT;');
    	dd('ok');
    }
}
