<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;
use App\Models\Country;
use App\Models\Favourite;
use DB;
use Auth;

class UserResource extends JsonResource
{   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = Auth::guard('api')->user();
        $is_favourite = false;
        if($user){
            $fav = Favourite::where(['user_id'=> $user->id,'buddy_id'=>$this->id])->first();
            if($fav != null){
                $is_favourite = true;
            }
        }
        return [
            'token'  => $this->when(isset($this->accessToken), $this->accessToken),
            'id' => $this->id ? (string)$this->id : '' ,
            'full_name' => (string)$this->first_name ?? "",
            'designation' => (string)$this->designation ?? "",
            'company_name' => (string)$this->company_name ?? "",
            'bio' => (string)$this->bio ?? "",
            'email' => (string)$this->email ?? "",
            'mobile_number' => (string)$this->mobile_number ?? "",
            'profile_image' => $this->profile_image ? asset($this->profile_image) : asset('customer_avtar.jpeg'),
            'country' => new CountryResource($this->country),
            'preferred_language' => (string)$this->preferred_language,
            'is_social_login' => ($this->social_type == 'social') ? "1" : "0",
            'is_favourite'   => $is_favourite,    
        ];
    }
}
