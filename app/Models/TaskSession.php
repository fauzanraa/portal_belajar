<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSession extends Model
{
    protected $table = 'task_sessions';
    // protected $fillable = ['name', 'email', 'password'];

    public function meeting(){
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function studentTaskSession(){
        return $this->hasMany(StudentTaskSession::class, 'task_session_id');
    }
}
