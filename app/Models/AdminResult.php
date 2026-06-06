<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminResult extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'plays',
        'avg_score',
        'total_correct',
        'total_questions',
    ];

    protected $appends = ['board', 'standard', 'subject'];

    public function subjectRel()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function getBoardAttribute()
    {
        return $this->subjectRel?->board?->name;
    }

    public function getStandardAttribute()
    {
        return $this->subjectRel?->standard?->name;
    }

    public function getSubjectAttribute()
    {
        return $this->subjectRel?->name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
