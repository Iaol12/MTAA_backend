<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{

    public $timestamps = false;

    protected $fillable = ['role_name', 'privilege'];

    public function users()
    {
        return $this->hasMany(User::class, 'user_role');
    }
}

