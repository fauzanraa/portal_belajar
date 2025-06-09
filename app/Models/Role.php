<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // protected $table = 'roles';
    // protected $fillable = ['name', 'email', 'password'];

    public function users()
    {
        return $this->belongsToMany(UserSystem::class, 'role_users', 'role_id', 'user_system_id');
    }
}
