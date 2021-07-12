<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['state_id','name','status'];

    /**
     * @return mixed
     */

   public function state()
    {
        return $this->belongsTo(State::class,'state_id');
    }

}