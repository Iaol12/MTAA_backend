<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'discounted_tickets', 'delay'];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

