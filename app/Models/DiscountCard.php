<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCard extends Model
{
    protected $fillable = ['zlava_id', 'zlavovy_kod'];

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'zlava_id');
    }
}
