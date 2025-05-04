<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'train_id',
        'start_station',
        'end_station',
        'platny_od',
        'platny_do',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function train()
    {
        return $this->belongsTo(Train::class);
    }

    public function startStation()
    {
        return $this->belongsTo(Station::class, 'start_station');
    }

    public function endStation()
    {
        return $this->belongsTo(Station::class, 'end_station');
    }
}

