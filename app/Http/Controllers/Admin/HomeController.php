<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\State;
use Auth;
use App;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
      $user = auth()->user();
      
      

      if($user->user_type == 'admin') {

          $total_customers  = User::where('user_type','customer')->count();

          
          return view('admin.dashboard.admin',compact('user', 'total_customers'));
      }

      
      if($user->user_type == 'developer') {
        return redirect()->route('permissions.index'); 
      }
    }

     public function get_state($country_id){
      try {
          $states = State::where('country_id',$country_id)->where('status','active')->get();
          return response()->json(['success' => '1', 'data' => $states, 'message' => 'state_list']);
      } catch (Exception $e) {
          return response()->json(['success' => '0', 'data' => [], 'message' => $e->getMessage()]);
      }
    }

    //Localization function
    public function lang($locale){
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
