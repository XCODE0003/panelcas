<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\HasName;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Worker extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     public function getFilamentAvatarUrl(): ?string
     {
         return null;
     }
     public function getFilamentName(): string
     {
         return $this->username;
     }
     public function notify_settings()
     {
        return $this->hasOne(NotifySetting::class, 'user_id', 'id');
     }
    protected $fillable = [
        'username',
        'tg_username',
        'tg_id',
        'is_ban',
        'balance',
        'is_admin',
        'is_support',
        'password',
        'notify',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_ban == 0;
    }
}
