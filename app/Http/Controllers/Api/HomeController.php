<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Models\Setting;
use App\Models\DeviceDetail;
use Validator;
use Auth,DB;

class HomeController extends BaseController
{
    /**
     * Update device Token.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_device_token(Request $request) {

        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type'  => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError('', $validator->errors()->first());       
        }

        $data = [
            'device_token' => $request->device_token,
            'device_type'  => $request->device_type,
        ];
        $createArray = array();
        
        foreach ($data as $key => $value) {
            $createArray[$key] = $value;
        }
        $device_detail = DeviceDetail::where('user_id',Auth::user()->id)->first();
        if($device_detail){
            $res = $device_detail->update($createArray);
        } else {
            $createArray['user_id'] = Auth::user()->id;
            $res = DeviceDetail::create($createArray);
        }
        if($res)
        {
            return $this->sendResponse('',trans('common.token_updated'));
        }
        else
        {
            return $this->sendError('',trans('common.token_can_not_be_updated'));
        }
    }

}
