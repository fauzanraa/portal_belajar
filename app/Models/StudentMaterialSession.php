<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMaterialSession extends Model
{
    protected $table = 'student_material_sessions';
    // protected $fillable = ['name', 'email', 'password'];

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function materialSession(){
        return $this->belongsTo(MaterialSession::class, 'material_session_id');
    }
}
