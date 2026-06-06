<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = ['email', 'otp', 'type', 'expires_at', 'used_at'];

    public function scopeValid($q)
    {
        return $q->whereNull('used_at')->where('expires_at', '>', now());
    }
}
