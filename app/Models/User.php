<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'is_verification',
        'win_chance',
        'inviter',
    ];

    public function inviterUser(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'inviter', 'id');
    }
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

   
}

