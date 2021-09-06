<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\City;
use App\Models\SmsVerification;
use App\Models\Country;
use App\Models\EmailVerification;
use Carbon\Carbon;
use Validator,DB,Notification;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use App\Models\Helpers\CommonHelper;
use App\Notifications\SendVerificationEmail;
use Illuminate\Support\Facades\Hash;
use Authy\AuthyApi;

class ProfileController extends BaseController
{
    use CommonHelper;

    public function profile(Request $request)
    {
        $user = Auth::user();
        return $this->sendResponse(new UserResource($user), trans('profile.profile_details'));
    }

    /**
     * Update Profile
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request)
    {
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|regex:/^[\w\-\s]+$/u|min:3|max:30',
            'designation'   => 'required|min:3|max:50',
            'company_name'  => 'required|string|min:3|max:50',
            'bio'           => 'required|string|max:500',
            'email'         => 'required|email|max:100|unique:users,email,'.$id,
            // 'mobile_number' => 'required|digits:10|unique:users',
            // 'country_id'    => 'required|exists:countries,id',
        ]);
        if($validator->fails()){
            return $this->sendValidationError('', $validator->errors()->first());       
        }
        
        DB::beginTransaction();
        try{
            $user = Auth::user();     
            $updateArray = [
                'first_name'    => $request->full_name,
                'designation'   => $request->designation,
                'company_name'  => $request->company_name,
                'bio'           => $request->bio,
                'email'         => $request->email,
                // 'mobile_number' => $request->mobile_number,
                // 'country_id'    => $request->country_id,
            ];
            
            $user->fill($updateArray);
            if($user->save()){
                DB::commit();
                return $this->sendResponse(new UserResource($user), trans('profile.profile_update_success'));
            }else{
                DB::rollback();
                return $this->sendError('',trans('profile.profile_update_error'));
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Update Profile Image
     *
     * @return \Illuminate\Http\Response
     */
    public function update_image(Request $request)
    {
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:png,jpg,jpeg,svg|max:10000',
        ]);
        if($validator->fails()){
            return $this->sendValidationError('', $validator->errors()->first());       
        }
        
        DB::beginTransaction();
        try{
            $user = Auth::user();
            $updateArray = array();
            if(isset($request->profile_image)){
                if(file_exists($user->profile_image)){
                  unlink($user->profile_image);
                }
              $path = $this->saveMedia($request->file('profile_image'),'profile');
              $updateArray['profile_image'] = $path;
            }
            $user->fill($updateArray);
            if($user->save()){
                DB::commit();
                return $this->sendResponse(new UserResource($user), trans('profile.profile_update_success'));
            }else{
                DB::rollback();
                return $this->sendError('',trans('profile.profile_update_error'));
            }
        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Update Mobile Number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_mobile_number(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|digits:10|unique:users,mobile_number,NULL,id,user_type,customer',
            'country_id'    => 'required|exists:countries,id',
        ]);
        if($validator->fails()){
            return $this->sendValidationError('', $validator->errors()->first());       
        }

        $user = Auth::user();
        $country = Country::find($request->country_id);
        

        $user->mobile_number = $request->mobile_number;
        $user->verified = '1';
        $res = $user->save();
        //send and store otp
        // $otp = $this->genrateOtp();
        // $country_code = $country->country_code;
        // $mobile_number = $request->mobile_number;
        // $res = $this->sendOtp($country_code,$mobile_number,$otp);


        if($res){
            // return $this->sendResponse(new UserResource($user), trans('auth.otp_sent',['number'=>$request->mobile_number]));
            return $this->sendResponse(new UserResource($user), trans('auth.mobile_changed'));
        } else {
            return $this->sendError('',trans('auth.otp_sent_error'));
        }
    }

    /**
     * Verify Mobile Number via OTP
     * @return \Illuminate\Http\Response
     */
    public function verifyMobileNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|digits:10',
            'country_id'    => 'required|exists:countries,id',
            'otp'           => 'required|min:4|max:4'
        ]);

        if($validator->fails()) {
            return $this->sendError($this->object, $validator->errors()->first());       
        }

        $user = User::find(Auth::user()->id);

        $smsVerifcation = SmsVerification::where(['mobile_number' => $request->mobile_number,'status' => 'pending'])
                        ->latest() //show the latest if there are multiple
                        ->first();

        if($smsVerifcation == null){
            return $this->sendError($this->object,trans('auth.number_not_found'));
        }

        if($user->country_id != $request->country_id){
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

            $user->mobile_number = $request->mobile_number;
            $user->verified = '1';
            $user->save();

            $request["status"] = 'verified';
            $smsVerifcation->updateModel($request);
             
            DB::commit();
            return $this->sendResponse(new UserResource($user), trans('auth.mobile_changed'));
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError($this->object,$e->getMessage());
        }       
       
    }

}
