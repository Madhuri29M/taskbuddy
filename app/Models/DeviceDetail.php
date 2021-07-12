<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceDetail extends Model
{
    protected $table = 'device_detail';

    protected $fillable = [
  		'user_id','device_token','device_type','uuid','ip','os_version','model_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','id');
    }
}
