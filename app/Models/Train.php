<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    protected $fillable = ['name', 'delay'];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

