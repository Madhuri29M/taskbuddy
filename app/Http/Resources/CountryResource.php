<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'id'   => $this->id ? (string)$this->id : '' ,
            'name' => $this->name ? (string)$this->name : '' ,
            'code' => $this->country_code ? (string)$this->country_code : '' ,
            'flag' => asset($this->flag)
        ];
    }
}
