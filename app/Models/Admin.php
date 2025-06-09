<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';
    // protected $fillable = ['name', 'email', 'password'];

    // public function user()
    // {
    //     return $this->belongsTo(UserSystem::class, 'user_system_id');
    // }

    public function userSystem(){
        return $this->morphMany(UserSystem::class, 'userable');
    }
}
