<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachemnt extends Model
{
    
    protected $table = 'task_attachments';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image','task_id'
    ];

    public function task()
    {
        return $this->belongsTo('App\Models\Task','task_id','id');
    }

}
