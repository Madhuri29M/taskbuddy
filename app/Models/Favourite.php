<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
	protected $table = 'favourites';

    protected $fillable = ['user_id', 'buddy_id'];

    /**
     * @return mixed
     */


    // user
    public function user()
    {
      return $this->hasOne('App\Models\User','id','user_id');
    }

    // buddy
    public function buddy()
    {
      return $this->hasOne('App\Models\User','id','buddy_id');
    }


}