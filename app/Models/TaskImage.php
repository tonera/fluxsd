<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskImage extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function task():BelongsTo
    {
        return $this->belongsTo(Task::class,'task_id', 'task_id');
    }
}
