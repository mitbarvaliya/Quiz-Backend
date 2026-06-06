<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'board',
        'standard',
        'total_points',
        'total_questions',
        'percentage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjectRel()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
