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
      $data_id =  ($this->service_id)  ? (string)$this->service_id : "";
      if($this->slug == 'ticket_status_updated') {
        $data_id = ($this->data_id)  ? (string)$this->data_id : "";
      }
      $title = $this->trans_title ? (string)$this->trans_title : (string)$this->title;
      $content = $this->trans_content ? (string)$this->trans_content : (string)$this->content;
      if($this->slug == 'admin_broadcast')
      {
        $title = $this->title;
        $content = $this->content;
      }
      return [
        'id'      => $this->id ? (string)$this->id : '' ,
        /*'title'   => (string)$this->title,
        'content' => (string)$this->content,*/
        'title'  => $title,
        'content'     => $content,
        'type'    => (string)$this->slug,
        'booking_service_id' => $data_id,
        'is_read' => $this->is_read,
        'date'    => Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans(),
      ];
  	}
}
