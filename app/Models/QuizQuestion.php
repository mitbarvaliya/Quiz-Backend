<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'subject_id',
        'level',
        'question_id',
        'question',
        'options',
        'correct',
    ];

    protected $casts = [
        'options' => 'array',
        'correct' => 'integer',
    ];

    protected $appends = ['board', 'standard', 'stream', 'subject'];

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

    public function getStreamAttribute()
    {
        return $this->subjectRel?->stream;
    }

    public function originalQuestion()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
