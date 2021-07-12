<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function clear_cache()
    {
      Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('config:cache');
      Artisan::call('route:clear');
      Artisan::call('route:cache');
      Artisan::call('view:clear');
      Artisan::call('view:cache');
      Artisan::call('optimize:clear');
      return "Application Cache is cleared"; 
    }
    
}