<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Http\Resources\UserResource;
use Validator;
use Auth,DB;

class UserController extends BaseController
{
 
  /**
   * User Details.
   *
   * @return \Illuminate\Http\Response
   */
  public function show() {
    $user = auth()->user();
    // return $user;
    if($user){
      $user_data = new UserResource($user);
      // print_r($user_data); die;
      return $this->sendResponse($user_data, 'User details got successfully');
    }else{
      return $this->sendError([], 'You are unauthorised.');
    }
  }


  /**
   * User update_user Details.
   *
   * @return \Illuminate\Http\Response
   */
  public function update_user(Request $request){
    $validator = $request->validate([
          'name' => 'required|min:2|max:50',
          'last_name'  => 'required|max:50',
          'email'      => 'required|email|max:150',
          'password'   => 'sometimes|min:8|max:14',
          // 'phone_number' => 'required|min:8|max:15',
          'birth_date' => 'sometimes|nullable|date_format:Y-m-d',
    ]);

    if($validator->fails()) {
        return $this->sendValidationError('', $validator->errors()->first());       
    }

    // print_r($request->all()); die;

    $user = Auth::user();
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->email = $request->email;
    if($request->password){
      $user->password = Hash::make($input['new_password']);
    }
    if($request->birth_date) {
      $user->birth_date = $request->birth_date;
    }
    //$user->phone_number = $request->phone_number;
    $user->save();
   
    if($user) {
      $user_data = new UserResource($user);
      // print_r($user_data); die;
      return $this->sendResponse($user_data, trans('auth.user_updated'));
    } else {
      return $this->sendError([], trans('auth.unauthenticated'));
    }
  }

  public function preferred_language($lang = '') {
    if(!$lang || $lang == '') {
      return $this->sendError('',trans('auth.language_not_found'));
    }

    $all_langs = [];
    foreach(config('app.locales') as $avail_lang=>$avail_lang_data) {
      array_push($all_langs, $avail_lang);
    }

    if(in_array($lang, $all_langs)) {
      $user = auth()->user();
      $user->preferred_language = $lang;
      $user = $user->save();
      if($user) {
        return $this->sendResponse('',trans('auth.language_updated'));
      }else{
          return $this->sendResponse('',trans('common.something_went_wrong'));
      } 
    } else {
      return $this->sendError('',trans('auth.language_not_found'));
    }
  }
}
