<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Country;

class State extends Model
{
    protected $fillable = ['country_id','name','status'];

    /**
     * @return mixed
     */
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id' );
    }

     public function city()
    {
        return $this->hasMany('App\Models\City','state_id','id' );
    }
}
