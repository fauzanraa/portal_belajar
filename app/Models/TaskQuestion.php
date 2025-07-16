<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskQuestion extends Model
{
    protected $table = 'task_questions';
    protected $fillable = [
        'task_session_id',
        'question',
        'type',
        'correct_answer',
        'flowchart_img',
        'required_components'
    ];

    protected $casts = [
        'required_components' => 'array' 
    ];
}
