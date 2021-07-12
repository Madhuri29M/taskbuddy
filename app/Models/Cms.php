<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\CmsTranslation;
use Spatie\Permission\Models\Role;

class Cms extends Model
{
	use Translatable;

    protected $table = 'cms';

    protected $fillable = ['slug', 'display_order', 'status'];

    /**
     * The localed attributes that are mass assignable.
     *
     * @var array
     */
    public $translatedAttributes = ['page_name','content',];

    /**
     * @var string
     */
    public $translationForeignKey = 'cms_id';

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
    public $translationModel = CmsTranslation::class;

    // function for filter records
    public function translation(){
    	return $this->hasMany(CmsTranslation::class, 'cms_id','id');
    }

    // function for filter records
    public function singleTranslation(){
        return $this->hasOne(CmsTranslation::class, 'cms_id','id');
    }
}
