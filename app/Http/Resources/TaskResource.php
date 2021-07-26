<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Guest;
use App\Models\Setting;
use App\Models\Country;
use App\Models\State;
use DB;
use Auth;

class TaskResource extends JsonResource
{   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $attachments = [];
        if($this->attachments)
        {
            foreach ($this->attachments as $key => $attachment) {
                $attachments[$key]['attachment_id'] = (string)$attachment->id;
                $attachments[$key]['attachment'] = asset($attachment->attachment);
            }
        }

        $task_history = [];
        if($this->task_history)
        {
            foreach ($this->task_history as $key => $history) {
                $task_history[$key]['status'] = $history->status;
                $task_history[$key]['updated_date'] = date('d M Y',strtotime($history->created_at));
                $task_history[$key]['updated_time'] = date('h:i A',strtotime($history->created_at));
            }
        }
        return [
            'id' => $this->id ? (string)$this->id : '' ,
            'title' => $this->title ? (string)$this->title : '' ,
            'description' => $this->description ? (string)$this->description : '' ,
            'assigned_by' => $this->assignedBy ? new UserResource($this->assignedBy) : null ,
            'due_date' => $this->due_date ? (string)date('d M Y',strtotime($this->due_date)) : '' ,
            'due_time' => $this->due_time ? (string)date('h:i A',strtotime($this->due_time)) : '' ,
            'status' => ucfirst($this->status),
            'attachments' => $attachments,
            'task_history' => $task_history
        ];
    }
}
