<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    
    const ACTIVE_STATUS   = '1';
    const INACTIVE_STATUS = '0';

    protected $table = 'countries';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'country_code','status'
    ];

}
