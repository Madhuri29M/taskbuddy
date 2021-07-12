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
   /* $data = Cms::with(['singleTranslation'=>function($q) use ($locale){
              $q->where('locale',$locale);
            }])->where('slug','privacy_policy')->first();*/

    
    $cms = Cms::where('slug','privacy_policy')->first();
    
    /*$cms = new \stdClass;
    $cms->slug      = $data->slug;
    $cms->page_name = $data->singleTranslation['page_name'];
    $cms->content   = $data->singleTranslation['content'];*/

    return view('admin.cms.view',compact('cms'));    
  }

  public function customer_terms_conditions_url($locale){
    \App::setLocale($locale);
    /*$data = Cms::with(['singleTranslation'=>function($q) use ($locale){
              $q->where('locale',$locale);
            }])->where('slug','terms_conditions')->first();

    $cms = new \stdClass;
    $cms->slug      = $data->slug;
    $cms->page_name = $data->singleTranslation['page_name'];
    $cms->content   = $data->singleTranslation['content'];*/

    $cms = Cms::where('slug','terms_conditions')->first();

    return view('admin.cms.view',compact('cms'));
  }

  public function cancel_policy_url($locale){
    \App::setLocale($locale);
    /*$data = Cms::with(['singleTranslation'=>function($q) use ($locale){
              $q->where('locale',$locale);
            }])->where('slug','cancel_policy')->first();

    $cms = new \stdClass;
    $cms->slug      = $data->slug;
    $cms->page_name = $data->singleTranslation['page_name'];
    $cms->content   = $data->singleTranslation['content'];*/

    $cms = Cms::where('slug','cancel_policy')->first();

    return view('admin.cms.view',compact('cms'));
  }

  public function why_this_advertisement_url($locale){
    \App::setLocale($locale);
    /*$data = Cms::with(['singleTranslation'=>function($q) use ($locale){
              $q->where('locale',$locale);
            }])->where('slug','why_you_are_seeing_this_advertisement')->first();

    $cms = new \stdClass;
    $cms->slug      = $data->slug;
    $cms->page_name = $data->singleTranslation['page_name'];
    $cms->content   = $data->singleTranslation['content'];*/

    $cms = Cms::where('slug','why_you_are_seeing_this_advertisement')->first();

    return view('admin.cms.view',compact('cms'));
  }

  public function faq_url($locale){
    \App::setLocale($locale);
    $title = 'FAQs';
    $data = Faq::with(['singleTranslation'=>function($q) use ($locale){
                $q->where('locale',$locale);
              }])->where('status','active')->get();

    $faqs = new \stdClass;
    foreach ($data as $key => $value) {
      $faq_data = new \stdClass;
      $faq_data->question = $value->singleTranslation['question'];
      $faq_data->answer = $value->singleTranslation['answer'];

      $faqs->$key = $faq_data;
    }
    return view('customer.faq_for_api', compact('faqs', 'title'));
  }
}