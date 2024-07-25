<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    public function index(){
    	DB::statement('ALTER TABLE businesses ADD status VARCHAR(255);');
    	dd('ok');
    }
}
