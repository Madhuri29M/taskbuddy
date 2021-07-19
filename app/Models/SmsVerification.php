<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsVerification extends Model
{
    protected $fillable = [
 		'mobile_number','code','status' 
 	];

 	/* store */
 	public function store($request)
	{
		$this->fill($request->all());
		$sms = $this->save();
		return response()->json($sms, 200);
	}

	/* updateModel */
	public function updateModel($request)
 	{
 		$this->update($request->all());
 		return $this;
 	}
}
