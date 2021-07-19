<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Guest;
use App\Models\Setting;
use App\Models\Country;
use App\Models\State;
use DB;
use Auth;

class BuddyResource extends JsonResource
{   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $user = new UserResource($this->user_1);
        if($this->user1 == Auth::guard('api')->user()->id)
        {
            $user = new UserResource($this->user_2);
        }
        return [
            'request_id' => $this->id ? (string)$this->id : '' ,
            'user' => $user         
        ];
    }
}
