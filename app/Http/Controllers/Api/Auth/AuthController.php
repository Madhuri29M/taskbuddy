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
            // 'firebase_token' => 'required|unique:users',
            // 'device_token'   => 'required',
            // 'device_type'    => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError($this->object, $validator->errors()->first());       
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
        $user->user_type      = 'customer';
        $user->status         = 'active';


        DB::beginTransaction();
        try {

            if($user->save()){ // Check if user data is saved


                // Save Device Details
                $data = $request->except('full_name','company_name','mobile_number','country_id','designation');
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

                /*$otp = $this->genrateOtp();
                $country_code = $user->country->country_code;
                $mobile_number = $request->mobile_number;
                $res = $this->sendOtp($country_code,$mobile_number,$otp);

                if($res)
                {
                    //send welcome email to customer
                    return $this->sendResponse(new UserResource($user), trans('auth.otp_sent', ['number'=> $request->mobile_number]));
                }
                else{
                    return $this->sendError('',trans('auth.otp_sent_error'));
                }*/
                DB::commit();
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
            // 'firebase_token' => 'required|exists:users',
        ],[
            'mobile_number.exists' => 'This mobile number is not registered with us. Please register first.'
        ]);
        if($validator->fails()) {
            return $this->sendError($this->object, $validator->errors()->first());       
        }

        $user = User::where('mobile_number', $request->mobile_number)->where('user_type','customer')->first();

        if(!$user){
            return $this->sendError($this->object,trans('auth.user_not_valid'));
        }
        if($request->device_type != 'iphone' && $request->device_type != 'android'){
            return $this->sendError('',trans('auth.device_type_error'));
        }
        /*if($user->firebase_token != $request->firebase_token)
        {
            return $this->sendError($this->object,trans('auth.firebase_token_not_valid'));
        }*/

        if($user->status == 'blocked' || $user->status == 'inactive'){
            $admin_email = Setting::get('contact_email');
            return $this->sendError($this->object,trans('auth.account_blocked',['contact' => $admin_email]));
        }

        $smsVerifcation = SmsVerification::where(['mobile_number' => $request->mobile_number])
                    ->latest() //show the latest if there are multiple
                    ->first();
        //send & store otp
        /*$otp = $this->genrateOtp();
        $country_code = $user->country->country_code;
        $mobile_number = $request->mobile_number;
        $res = $this->sendOtp($country_code,$mobile_number,$otp,$smsVerifcation);*/

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

        /*if($res) {
            return $this->sendResponse(new UserResource($user), trans('auth.otp_sent', ['number'=> $request->mobile_number]));
        }
        else
        {
            return $this->sendError('',trans('auth.otp_sent_error'));
        }*/
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

            $mobile_rules = 'nullable|digits:10|unique:users';
            $email_rules = 'nullable|email|unique:users';
            if($customer){
                $mobile_rules = 'nullable|unique:users,mobile_number,'.$customer->id.',id,user_type,customer|digits:10';
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
                if(!$request->mobile_number){
                    return $this->sendResponse('', trans('auth.account_not_exist_mobile_required'), '404');
                }
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
                if($request->image_url)
                {
                    $path = $this->saveImageFromUrl($request->image_url);
                    $customer->profile_image = $path;
                }
                $customer->save();

                //send and store otp
                /*$otp = $this->genrateOtp();
                $country_code = $customer->country->country_code;
                $mobile_number = $request->mobile_number;
                $res = $this->sendOtp($country_code,$mobile_number,$otp);

                if($res) {
                    return $this->sendResponse(new UserResource($customer), trans('auth.otp_sent', ['number'=> $request->mobile_number]));
                } else {
                    return $this->sendError('',trans('auth.otp_sent_error'));
                }*/

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
                if($customer->verified != '1'){
                    if(!$request->mobile_number){
                        return $this->sendResponse('', trans('auth.account_exist_mobile_required'), '404');
                    }

                    $customer->mobile_number = $request->mobile_number;
                    $customer->country_id = $request->country_id;
                    $customer->save();

                    //send and store otp
                    /*$otp = $this->genrateOtp();
                    $country_code = $customer->country->country_code;
                    $mobile_number = $request->mobile_number;
                    $res = $this->sendOtp($country_code,$mobile_number,$otp);
                    
                    if($res) {
                        return $this->sendResponse(new UserResource($customer), trans('auth.otp_sent', ['number'=> $request->mobile_number]));
                    }
                    else{
                        return $this->sendError('',trans('auth.otp_sent_error'));
                    }*/
                }
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

    /**
     * Verify OTP
     * @return \Illuminate\Http\Response
     */
    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
            'mobile_number' => 'required|digits:10',
            'country_id'    => 'required|exists:countries,id',
            'otp'           => 'required|min:4|max:4',
            'device_token'  => 'required',
            'device_type'   => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError($this->object, $validator->errors()->first());       
        }

        if($request->device_type != 'iphone' && $request->device_type != 'android'){
            return $this->sendError('',trans('auth.device_type_error'));
        }
        
        $user = User::find($request->user_id);

        // Save Device Details
        $data = $request->except('user_id','mobile_number','country_id','otp');
        $createArray = array();
        
        foreach ($data as $key => $value) {
            $createArray[$key] = $value;
        }
        \Log::info($createArray);
        $device_detail = DeviceDetail::where('user_id',$user->id)->first();
        if($device_detail){
            $device_detail->update($createArray);
        } else {
            $createArray['user_id'] = $user->id;
            DeviceDetail::create($createArray);
        }

        $smsVerifcation = SmsVerification::where(['mobile_number' => $request->mobile_number,'status' => 'pending'])
                        ->latest() //show the latest if there are multiple
                        ->first();

        if($smsVerifcation == null){
            return $this->sendError($this->object,trans('auth.number_not_found'));
        }

        if($user->country_id != $request->country_id){
            return $this->sendError('', trans('auth.otp_wrong_number'));
        }

        if($user->mobile_number != $request->mobile_number){
            return $this->sendError('', trans('auth.otp_wrong_number'));
        }

        if($request->otp != $smsVerifcation->code){
            return $this->sendError($this->object,trans('auth.otp_invalid_long'));
        }



        $otp_time_difference_in_minutes = $smsVerifcation->created_at->diffInMinutes(Carbon::now());

        //Checking OTP Code Expiry
        if($otp_time_difference_in_minutes > config('adminlte.otp_expiry_in_minutes')) {
            $request["status"] = 'expired';
            $request['code'] = $request['otp'];
            $smsVerifcation->updateModel($request);
            return $this->sendError($this->object,trans('auth.otp_expired_long'));
        }

      
        DB::beginTransaction();
        try {

            $user->verified = "1";
            $user->save();

            $request["status"] = 'verified';
            $smsVerifcation->updateModel($request);
            
            // $response['token']  =  $user->createToken(config('app.name'))->accessToken;
            // $response['user'] = new UserResource($user);
            $user->accessToken = $user->createToken(config('app.name'))->accessToken;
             
            DB::commit();
            return $this->sendResponse(new UserResource($user), trans('auth.mobile_verified'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError($this->object,$e->getMessage());
        }       
       
    }

    /**
     * Resend OTP
     * 
     */
    public function resendOTP(Request $request)
    {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'user_id'        => 'required|exists:users,id',
                'mobile_number'  => 'required|digits:10',
                'country_id'    => 'required|exists:countries,id',
            ]);
            if($validator->fails()) {
                return $this->sendError($this->object, $validator->errors()->first());       
            }
            $user = User::find($request->user_id);

            if($user->country_id != $request->country_id){
                return $this->sendError('', trans('auth.otp_wrong_number'));
            }
            /*if($user->mobile_number != $request->mobile_number){
                return $this->sendError('', trans('auth.otp_wrong_number'));
            }*/

            /*if($user->verified == '1'){
                return $this->sendResponse('', trans('auth.number_active'));
            }*/

            $smsVerifcation = SmsVerification::where(['mobile_number' => $request->mobile_number])
                            ->latest() //show the latest if there are multiple
                            ->first();

            if(!$smsVerifcation){
                return $this->sendResponse($this->object, trans('auth.number_not_found'));
            }

            //resend otp to mobile number and update 
            $otp = $this->genrateOtp();
            $country_code = $user->country->country_code;
            $mobile_number = $request->mobile_number;
            $res = $this->sendOtp($country_code,$mobile_number,$otp,$smsVerifcation);

            /*$smsVerifcation->code   = '1234';
            $smsVerifcation->status = 'pending';
            $smsVerifcation->created_at = Carbon::now();
            $smsVerifcation->save();*/
            DB::commit();
            if($res){
                return $this->sendResponse('', trans('auth.otp_sent', ['number'=> $request->mobile_number]));
            } else {
                return $this->sendError('',trans('auth.otp_sent_error'));
            }
        } catch (\Exception $e) {   
            DB::rollback();
            $code = $e->getCode();
            $message = $e->getMessage();
            $response = array('code'=>$code,'message'=>$message);
            return $this->sendError('', trans('auth.otp_sent', $response));
        }
    }
}