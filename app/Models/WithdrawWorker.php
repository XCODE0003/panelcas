<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawWorker extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'address', 'status', 'amount', 'description', 'currency'];

    public function user()
    {
        return $this->belongsTo(Worker::class);
    }
}
