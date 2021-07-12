<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeviceDetail;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SignupActivate;
// use App\Models\Helpers\AuthHelpers;
use App\Models\Helpers\CommonHelper;
use Carbon\Carbon;
use App\Models\SmsVerification;
use App\Models\Setting;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Password;
use DB;
use Validator;

class AuthController extends BaseController
{
    use CommonHelper;

    /**
     * Signup api
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'full_name'      => 'required|regex:/^[\w\-\s]+$/u|min:3|max:30',
            'designation'    => 'required|regex:/^[\w\-\s]+$/u|min:3|max:50',
            'company_name'   => 'required|string|min:3|max:50',
            'mobile_number'  => 'required|digits:10|unique:users',
            'country_id'     => 'required|exists:countries,id',
            'firebase_token' => 'required|unique:users',
            'device_token'   => 'required',
            'device_type'    => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError($this->object, $validator->errors()->first());       
        }

        if($request->device_type != 'iphone' && $request->device_type != 'android'){
            return $this->sendError('',trans('auth.device_type_error'));
        }

        // Create New User Eloquent Instance
        $input                = $request->all();
        $user                 = new User();
        $user->first_name     = $request->full_name;
        $user->designation    = $request->designation;
        $user->company_name   = $request->company_name;
        $user->email          = $request->email;
        $user->mobile_number  = $request->mobile_number;
        $user->password       = bcrypt(12345678);
        $user->country_id     = $request->country_id;
        $user->firebase_token = $request->firebase_token;
        $user->user_type      = 'customer';
        $user->status         = 'active';


        DB::beginTransaction();
        try {

            if($user->save()){ // Check if user data is saved
                // Save Device Details
                $data = $request->except('full_name','company_name','mobile_number','country_id','designation','firebase_token');
                $createArray = array();
                
                foreach ($data as $key => $value) {
                    $createArray[$key] = $value;
                }

                $device_detail = DeviceDetail::where('user_id',$user->id)->first();
                if($device_detail){
                    $device_detail->update($createArray);
                } else {
                    $createArray['user_id'] = $user->id;
                    DeviceDetail::create($createArray);
                }
                DB::commit();
                //send welcome email to customer
                $user->accessToken = $user->createToken(config('app.name'))->accessToken;
                return $this->sendResponse(new UserResource($user), trans('auth.registered_successfully'));
                
               
            }else{
                DB::rollback();
                return $this->sendError('',trans('auth.error'));
            }
        } catch (Exception $e) {
          DB::rollback();
          return $this->sendError('',trans('common.something_went_wrong'));
        }   
    }

    /**
     * Login api
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|digits:10|exists:users',
            'country_id'    => 'required|exists:countries,id',
            'device_token'  => 'nullable',
            'device_type'   => 'nullable',
            'firebase_token' => 'required|exists:users',
        ]);
        if($validator->fails()) {
            return $this->sendError($this->object, $validator->errors()->first());       
        }

        $user = User::where('mobile_number', $request->mobile_number)->where('user_type','customer')->first();

        if(!$user){
            return $this->sendError($this->object,trans('auth.user_not_valid'));
        }
        if($user->firebase_token != $request->firebase_token)
        {
            return $this->sendError($this->object,trans('auth.firebase_token_not_valid'));
        }

        if($user->status == 'blocked' || $user->status == 'inactive'){
            $admin_email = Setting::get('contact_email');
            return $this->sendError($this->object,trans('auth.account_blocked',['contact' => $admin_email]));
        }
        // Save Device Details
        $data = $request->except('mobile_number','country_id');
        $createArray = array();
        
        foreach ($data as $key => $value) {
            $createArray[$key] = $value;
        }
        $device_detail = DeviceDetail::where('user_id',$user->id)->first();
        if($device_detail){
            $device_detail->update($createArray);
        } else {
            $createArray['user_id'] = $user->id;
            DeviceDetail::create($createArray);
        }
        $user->accessToken = $user->createToken(config('app.name'))->accessToken;
        return $this->sendResponse(new UserResource($user), trans('auth.login_success'));

    }

    /**
     * Social Media Login : Google & Facebook
     *
     * @return \Illuminate\Http\Response
     */

    public function socialLogin(Request $request)
    {
        try{
            $customer = User::where('social_id', $request->social_id)->first();

            $mobile_rules = 'nullable|digits:9|unique:users';
            $email_rules = 'nullable|email|unique:users';
            if($customer){
                $mobile_rules = 'nullable|unique:users,mobile_number,'.$customer->id.',id,user_type,customer|digits:9';
                $email_rules = 'nullable|email|unique:users,email,'.$customer->id;
            }
            $validator = Validator::make($request->all(),[
                'provider'      => 'required',
                'token'         => 'required',
                'mobile_number' => $mobile_rules,
                'country_id'    => 'nullable|exists:countries,id',
                'device_token'  => 'nullable',
                'device_type'   => 'nullable',
                'name'          => 'nullable',
                'email'         => $email_rules,
                'image_url'     => 'nullable',
                'social_id'     => 'required'
            ]);
            
            if($validator->fails()){
              return $this->sendValidationError('',$validator->errors()->first());
            }
            if ( ! $customer ){
               
                $customer                = new User;
                $customer->mobile_number = $request->mobile_number;
                $customer->first_name    = $request->name ? $request->name : NULL;
                $customer->email         = $request->email ? $request->email : NULL;
                $customer->country_id    = $request->country_id;
                $customer->social_id     = $request->social_id;
                $customer->password      = bcrypt(12345678);
                $customer->status        = 'active';
                $customer->user_type     = 'customer';
                $customer->social_type   = 'social';
                /*if($request->image_url)
                {
                    $path = $this->saveImageFromUrl($request->image_url);
                    $customer->profile_image = $path;
                }*/
                $customer->save();

            } else {
                if($customer->first_name == ''){
                    $customer->first_name = $request->name;
                }
                if($customer->email == ''){
                    $customer->email = $request->email;
                }
                if($customer->profile_image == ''){
                    if($request->image_url){
                        $path = $this->saveImageFromUrl($request->image_url);
                        $customer->profile_image = $path;
                    }
                }
                $customer->save();
            }
            

            // Save Device Details
            $data = $request->except('provider','token','mobile_number','country_id','social_id','email');
            $createArray = array();
            
            foreach ($data as $key => $value) {
                $createArray[$key] = $value;
            }

            $device_detail = DeviceDetail::where('user_id',$customer->id)->first();
            if($device_detail){
                $device_detail->update($createArray);
            } else {
                $createArray['user_id'] = $customer->id;
                DeviceDetail::create($createArray);
            }
            
            $customer->accessToken = $customer->createToken(config('app.name'))->accessToken;

            return $this->sendResponse(new UserResource($customer), trans('auth.login_success'));
        } catch(\Exception $e) {
            return $this->sendError('',trans('common.something_went_wrong'));
        }
        
    }

    /**
     * Logout user (Revoke the token)
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if($user){
            DeviceDetail::where('user_id', $user->id)->delete();
            $user->token()->revoke();    
        }
          
        return $this->sendResponse('', trans('auth.logout_success'));
    }

    /**
     * Forgot password
     * @return \Illuminate\Http\Response
     */
    public function forgot_password(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if($validator->fails()) {

            return $this->sendError('', $validator->errors()->first());       
        }

        
        if(Password::sendResetLink($request->all())){
            
            return $this->sendResponse('', trans('auth.password_reset'));

        }else{

            return $this->sendError('',trans('auth.error'));
        }
    }
}