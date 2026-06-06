<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'board_id',
        'standard_id',
        'subject_id',
        'chapter_id',
        'question',
        'option_1',
        'option_2',
        'option_3',
        'option_4',
        'correct_answer',
    ];

    protected $casts = [
        'correct_answer' => 'integer',
    ];

    protected $appends = ['board', 'standard', 'stream', 'subject', 'chapter', 'options', 'correctAnswer'];

    public function boardRel()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    public function standardRel()
    {
        return $this->belongsTo(Standard::class, 'standard_id');
    }

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
        return $this->boardRel?->name;
    }

    public function getStandardAttribute()
    {
        return $this->standardRel?->name;
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

    public function getOptionsAttribute()
    {
        return [$this->option_1, $this->option_2, $this->option_3, $this->option_4];
    }

    public function getCorrectAnswerAttribute()
    {
        return $this->attributes['correct_answer'] - 1;
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_question')
            ->withTimestamps();
    }
}
