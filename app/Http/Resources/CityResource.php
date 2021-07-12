<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'state_id'      => $this->state_id ? (string)$this->state_id : '' ,
            'state_name'      => $this->state->name ? (string)$this->state->name : '' ,
            'name'          => ucfirst($this->name),
            // 'status'        =>  $this->status,
        ];
    }
}
