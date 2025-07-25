<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'classrooms';

    public function school(){
        return $this->belongsTo(School::class);
    }
}
