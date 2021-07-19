<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    protected $table = 'tasks_history';
    protected $guarded = [];

    /**
     * updatedBy
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class,'updated_by');
    }

}