<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ContactUsEmail;
use Illuminate\Http\Request;
use Validator;
use Notification;
use App\Models\Enquiry;
use App\Models\Setting;


class ContactUsController extends BaseController
{	
	public function contact_us(Request $request){
        $mobile_number = $this->ArToEn($request->mobile_number);
        $request->merge([
            'mobile_number' => $mobile_number,
        ]);
        $validator = Validator::make($request->all(),[
            'full_name'     => 'required|regex:/^[\w\-\s]+$/u|max:99',
            'email'         => 'nullable|email|max:255',
            'country_id'    => 'required|exists:countries,id',
            'mobile_number' => 'required|digits:9',
            'message'       => 'required|max:500'

        ]);
        if($validator->fails()){
          return $this->sendValidationError('',$validator->errors()->first());
        }
        $request->merge(['user_id' => Auth::guard('api')->user()->id]);
        $data = $request->all();
        $admin_email = Setting::get('contact_email');
        $response = Enquiry::create($data);
        Notification::route('mail', trim($admin_email))->notify(new ContactUsEmail($data));
        if($response){
            return $this->sendResponse('', trans('contact_us.enquiry_submitted'));
        }else{
            return $this->sendError('',trans('contact_us.enquiry_not_submitted'));
        }

    }

}
