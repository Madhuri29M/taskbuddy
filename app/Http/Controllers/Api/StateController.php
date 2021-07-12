<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use App\Models\State;
use App\Models\Country;
use App\Http\Resources\StateResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;

class StateController extends BaseController
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

    public function index($country_id = '') {
      // if(!$country_id || $country_id == ''){
      //   return $this->sendError('',trans('states.country_required'));
      // }
      $country_id = Country::whereIn('id',[1])->first()->id;
    	$states = State::where(['country_id' => $country_id, 'status' => 'active'])->whereHas('city')->get();
    	$states_data = StateResource::collection($states);
      if($states_data){
        if(count($states_data) > 0) {
          return $this->sendResponse($states_data,trans('states.states_found'));
        } else {
          return $this->sendResponse($states_data,trans('states.states_not_found')); 
        }
      }else{
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }
}
