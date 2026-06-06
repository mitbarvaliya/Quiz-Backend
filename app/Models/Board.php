<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ['name'];

    public function standards()
    {
        return $this->hasMany(Standard::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
