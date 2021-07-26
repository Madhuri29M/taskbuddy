<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable ,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'company_name',
        'designation',
        'password',
        'user_type',
        'mobile_number',
        'bio',
        'registered_on',
        'status',
        'profile_image',
        'preferred_language',
        'country_id',
        'social_id',
        'social_type',
        'verified',
        'email_verified_at',
        'firebase_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // vendor details
    public function profile()
    {
      return $this->hasOne('App\Models\Profile','user_id','id');
    }

    // vendor working hours
    public function working_hours()
    {
      return $this->hasMany('App\Models\VendorWorkingHour','vendor_id','id');
    }

    // associated city
    public function city()
    {
      return $this->belongsTo(City::class,'city_id');
    }

    // associated city
    public function country()
    {
      return $this->belongsTo(Country::class, 'country_id');
    }

    // Bank details
    public function bank()
    {
      return $this->hasOne('App\Models\BankDetail','vendor_id','id');
    }

    //addresses
    public function addresses(){
        return $this->hasMany('App\Models\Address','user_id');
    }

    public function device_detail()
    {
        return $this->hasOne('App\Models\DeviceDetail','user_id');
    }

    public function addNew($input)
    {
        $check = static::where('social_id',$input['social_id'])->first();
  
        if(is_null($check)){
          $update = static::where('email',$input['email'])->first();
          if($update){
             $update->update($input);
             return $update;
          }else{
            return static::create($input);
          }
        }
        return $check;
    }

    public function review()
    {
        return $this->hasOne('App\Models\Rating','user_id');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task','assigned_to');
    }

}
