<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\NotificationTranslation;

class Notifications extends Model
{
    use Translatable;
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    protected $fillable = ['user_id','data_id','user_type','title','content','is_sent','is_read','slug','cart_id','service_id'];

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }
    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['trans_title','trans_content'];

    /**
     * @var string
     */
    public $translationForeignKey = 'notification_id';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The class name for the localed model.
     *
     * @var string
     */
    public $translationModel = NotificationTranslation::class;

    // function for filter records
    public function translation(){

        return $this->hasMany(NotificationTranslation::class, 'notification_id','id');
    }
}
