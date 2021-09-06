<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DB;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      return [
        'id'       => $this->id ? (string)$this->id : '' ,
        'title'    => $this->title ? (string)$this->title : '' ,
        'content'  => $this->content ? (string)$this->content : '' ,
        'slug'     => (string)$this->slug,
        'buddy_id' => (string)$this->buddy_id,
        'task_id'  => (string)$this->task_id,
        'is_read'  => $this->is_read,
        'date'     => Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans(),
      ];
  	}
}
