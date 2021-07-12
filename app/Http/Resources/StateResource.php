<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
          return [
            'id'            => $this->id ? (string)$this->id : '' ,
            'country_id'    => $this->country_id ? (string)$this->country_id : '' ,
            'country_name'      => $this->country->name ? (string)$this->country->name : '' ,
            // 'country_id'    => $this->country_id ? (string)$this->country_id : '' ,
            'name'          => ucfirst($this->name),
            // 'status'        =>  $this->status,
        ];
    }
}
