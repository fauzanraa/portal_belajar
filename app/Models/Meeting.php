<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meetings';
    // protected $fillable = ['name', 'email', 'password'];

    public function material(){
        return $this->hasMany(MaterialSession::class);
    }

    public function task(){
        return $this->hasMany(TaskSession::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class, 'created_by', 'nip');
    }
}
