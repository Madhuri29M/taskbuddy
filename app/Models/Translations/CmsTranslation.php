<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cms;

class CmsTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_translations';


    public $translationModel = Cms::class;

    // function for filter records
    public function cms(){
        return $this->hasMany(Cms::class,'id','cms_id');
    }


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
    protected $fillable = ['page_name', 'content'];

    // function for filter records
    public function cms_data(){
        return $this->hasOne(Cms::class,'id','cms_id');
    }
}
