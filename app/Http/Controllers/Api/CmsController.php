<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Cms;
use App\Models\Setting;
use App\Models\Faq;

class CmsController extends BaseController
{	
	public function cms_page(){
      
      $locale = \App::getLocale();
      $response                      = array ();
      $response['privacy_page_url']  = route('cms.privacy_policy',$locale);
      $response['terms_page_url']    = route('cms.terms_and_conditions',$locale);
      $response['about_us']        = route('cms.about_us',$locale);

      if($response != null){
          return $this->sendResponse($response, trans('cms.cms_success'));
      }else{
          return $this->sendResponse($this->object,trans('cms.cms_error'));
      }
  }


  public function privacy_policy_url($locale){
    \App::setLocale($locale);    
    $cms = Cms::where('slug','privacy_policy')->first();
    return view('admin.cms.view',compact('cms'));    
  }

  public function customer_terms_conditions_url($locale){
    \App::setLocale($locale);
    $cms = Cms::where('slug','terms_conditions')->first();
    return view('admin.cms.view',compact('cms'));
  }

  public function about_us_url($locale){
    \App::setLocale($locale);
    $cms = Cms::where('slug','about_us')->first();
    return view('admin.cms.view',compact('cms'));
  }

}