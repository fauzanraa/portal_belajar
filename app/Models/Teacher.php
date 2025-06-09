<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';
    protected $fillable = ['user_system_id'];

    public function school(){
        return $this->belongsTo(School::class, 'school_id');
    }

    public function userSystem(){
        return $this->morphMany(UserSystem::class, 'userable');
    }

    public function meeting(){
        return $this->hasMany(Meeting::class, 'created_by', 'nip');
    }
}
