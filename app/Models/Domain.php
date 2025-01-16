<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'user_id',
        'ns_records',
        'title',
        'win_chance',
        'cloudflare_zone_id',
        'status',
    ];

    protected $casts = [
        'ns_records' => 'array',
    ];
}
