<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['board_id', 'standard_id', 'name', 'stream'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'subject_id');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'subject_id');
    }

    public function adminResults()
    {
        return $this->hasMany(AdminResult::class, 'subject_id');
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'subject_id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}
