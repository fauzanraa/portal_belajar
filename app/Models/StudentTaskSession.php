<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTaskSession extends Model
{
    protected $table = 'student_task_sessions';
    protected $fillable = [
        'task_session_id',
        'student_id',
        'score',
        'status',
        'duration',
        'finished_at',
        'access',
    ];

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function taskSession(){
        return $this->belongsTo(TaskSession::class, 'task_session_id');
    }

    public function taskQuestion(){
        return $this->hasMany(TaskQuestion::class, 'task_session_id');
    }

    public function taskAnswer(){
        return $this->hasMany(TaskAnswer::class, 'task_session_id');
    }
}
