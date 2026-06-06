<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    protected $fillable = ['board_id', 'name'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
