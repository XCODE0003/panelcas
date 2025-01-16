<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_id',
        'user_id',
    ];
}
