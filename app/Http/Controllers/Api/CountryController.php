<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use App\Models\Country;
use App\Http\Resources\CountryResource;
use Illuminate\Http\Request;
use DB,Validator,Auth;

class CountryController extends BaseController
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

    public function index() {
      // if(!$country_id || $country_id == ''){
      //   return $this->sendError('',trans('states.country_required'));
      // }
    	$countries = Country::where('status','1')->get();
    	$countries_data = CountryResource::collection($countries);
      if($countries_data){
        if(count($countries_data) > 0) {
          return $this->sendResponse($countries_data,trans('common.success'));
        } else {
          return $this->sendResponse('',trans('common.no_data')); 
        }
      }else{
        return $this->sendError('',trans('common.something_went_wrong')); 
      }
    }
}
