<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    public $timestamps = false;
    
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'password',
        'discount_id',
        'card_id',
        'user_role',
    ];

    protected $hidden = ['password'];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'user_role');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

