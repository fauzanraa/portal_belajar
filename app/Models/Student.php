<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    // protected $fillable = ['name', 'email', 'password'];

    public function school(){
        return $this->belongsTo(School::class, 'school_id');
    }

    public function classroom(){
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function userSystem(){
        return $this->morphMany(UserSystem::class, 'userable');
    }
}
