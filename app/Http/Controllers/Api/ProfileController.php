<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\City;
use App\Models\SmsVerification;
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
            'email'         => 'required|email|unique:users|max:100',
            'mobile_number' => 'required|digits:10|unique:users',
            'country_id'    => 'required|exists:countries,id',
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
                'mobile_number' => $request->mobile_number,
                'country_id'    => $request->country_id,
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

}
