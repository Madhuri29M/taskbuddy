<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;
use App\Models\Notifications;

class NotificationTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_translation';


    public $translationModel = Notifications::class;


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['trans_title','trans_content'];


    public function notification(){
        return $this->hasMany(Notifications::class,'id','notification_id');
    }
}