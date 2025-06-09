<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_users';
    protected $fillable = ['user_system_id', 'role_id'];

    public function users(){
        return $this->belongsTo(UserSystem::class, 'user_system_id');
    }

    public function roles(){
        return $this->belongsTo(Role::class, 'role_id');
    }
}
