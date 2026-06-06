<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'chapter_id',
        'level',
        'total_questions',
        'correct_answers',
        'time_taken',
        'details',
        'played_at',
    ];

    protected $casts = [
        'details' => 'array',
        'played_at' => 'datetime',
    ];

    protected $appends = ['board', 'standard', 'stream', 'subject', 'chapter'];

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

    public function chapterRel()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function getChapterAttribute()
    {
        return $this->chapterRel?->name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
