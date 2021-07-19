<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    protected $fillable = ['user_id','title','content','is_sent','is_read','slug','buddy_id'];

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function buddy(){
        return $this->belongsTo('App\Models\User','buddy_id');
    }
   
}
