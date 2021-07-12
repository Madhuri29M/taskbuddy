<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use App\Models\City;
use App\Http\Resources\CityResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;

class CityController extends BaseController
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

    public function index($state_id = '') {
      if(!$state_id || $state_id == ''){
        return $this->sendError('',trans('cities.country_required'));
      }
    	$cities = City::where('state_id',$state_id)->get();
    	$cities_data = CityResource::collection($cities);
      if($cities_data){
        if(count($cities_data) > 0) {
          return $this->sendResponse($cities_data,trans('cities.cities_found'));
        } else {
          return $this->sendResponse($cities_data,trans('cities.cities_not_found')); 
        }
      }else{
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }
}
