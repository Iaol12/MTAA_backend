<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    
    public $timestamps = false;

    protected $fillable = ['name'];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function startTickets()
    {
        return $this->hasMany(Ticket::class, 'start_station');
    }

    public function endTickets()
    {
        return $this->hasMany(Ticket::class, 'end_station');
    }
}

