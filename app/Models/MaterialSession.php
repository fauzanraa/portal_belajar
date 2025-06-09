<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialSession extends Model
{
    protected $table = 'material_sessions';
    // protected $fillable = ['name', 'email', 'password'];

    public function meeting(){
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    public function studentMaterialSession(){
        return $this->hasMany(StudentMaterialSession::class);
    }
}
