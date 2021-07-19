<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    protected $guarded = [];

    /**
     * assignedTo
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class,'assigned_to');
    }

    /**
     * AssignedBy
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class,'assigned_by');
    }

    /**
     * Attachments
     */
    public function attachments()
    {
        return $this->hasMany('App\Models\TaskAttachment','task_id','id');
    }

    /**
     * History
     */
    public function task_history()
    {
        return $this->hasMany('App\Models\TaskHistory','task_id','id');
    }
}