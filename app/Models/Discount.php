<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['nazov', 'vyska'];

    public function users()
    {
        return $this->hasMany(User::class, 'zlava_id');
    }

    public function discountCards()
    {
        return $this->hasMany(DiscountCard::class, 'zlava_id');
    }
}
