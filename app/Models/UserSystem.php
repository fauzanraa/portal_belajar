<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\Student;

class UserSystem extends Authenticatable
{   
    protected $fillable = ['username', 'password', 'userable_id', 'userable_type']; 

    protected $hidden = ['password'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_system_id', 'role_id');
    }

    public  function userable(){
        return $this->morphTo();
    }

    // public function admin(){
    //     return $this->hasOne(Admin::class, 'user_system_id');
    // }

    // public function teacher(){
    //     return $this->hasOne(Teacher::class, 'user_system_id');
    // }

    // public function student(){
    //     return $this->hasOne(Student::class, 'user_system_id');
    // }
}
