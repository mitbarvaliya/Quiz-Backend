<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'subject_id',
        'chapter_id',
        'level',
        'status',
    ];

    protected $appends = ['board', 'standard', 'stream', 'subject', 'chapter'];

    public function subjectRel()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function chapterRel()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
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

    public function getChapterAttribute()
    {
        return $this->chapterRel?->name;
    }

    public function getStreamAttribute()
    {
        return $this->subjectRel?->stream;
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_question')
            ->withTimestamps();
    }
}
