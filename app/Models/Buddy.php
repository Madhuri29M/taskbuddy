<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buddy extends Model
{

    protected $table = 'buddies';
    protected $guarded = [];

    /**
     * User 1
     */
    public function user_1()
    {
        return $this->belongsTo(User::class,'user1');
    }

    /**
     * User 2
     */
    public function user_2()
    {
        return $this->belongsTo(User::class,'user2');
    }
}