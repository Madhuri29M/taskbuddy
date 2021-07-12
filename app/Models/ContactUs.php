<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */

    const PENDING_STATUS         = 'pending';
    const ACKNOWLEDGE_STATUS     = 'acknowledged';
    const PROGRESSING_STATUS     = 'progressing';
    const RESOLVED_STATUS        = 'resolved';

    protected $table = 'contact_us';

    protected $fillable = [
        'user_id','user_type','reason_id','comment','description','status'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function reason(){
        return $this->belongsTo('App\Models\Reason');
    }
   
   public function reasons(){
        return $this->belongsTo('App\Models\Reason','reason_id');
    }
}
