<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = QuizQuestion::with('subjectRel.board', 'subjectRel.standard');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        return response()->json([
            'quiz_questions' => $query->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'level' => 'required|string|in:Easy,Medium,Hard',
            'question' => 'required|string',
            'options' => 'required|array|size:4',
            'options.*' => 'required|string',
            'correct' => 'required|integer|between:0,3',
            'question_id' => 'nullable|integer|exists:questions,id',
        ]);

        $quizQuestion = QuizQuestion::create([
            'subject_id' => $request->subject_id,
            'level' => $request->level,
            'question_id' => $request->question_id,
            'question' => $request->question,
            'options' => $request->options,
            'correct' => $request->correct,
        ]);

        return response()->json([
            'message' => 'Quiz question added successfully',
            'quiz_question' => $quizQuestion,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $quizQuestion = QuizQuestion::findOrFail($id);

        $request->validate([
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'level' => 'sometimes|required|string|in:Easy,Medium,Hard',
            'question' => 'sometimes|required|string',
            'options' => 'sometimes|required|array|size:4',
            'options.*' => 'required|string',
            'correct' => 'sometimes|required|integer|between:0,3',
            'question_id' => 'nullable|integer|exists:questions,id',
        ]);

        $quizQuestion->update($request->only([
            'subject_id', 'level', 'question_id', 'question', 'options', 'correct',
        ]));

        return response()->json([
            'message' => 'Quiz question updated successfully',
            'quiz_question' => $quizQuestion,
        ]);
    }

    public function destroy($id)
    {
        $quizQuestion = QuizQuestion::findOrFail($id);
        $quizQuestion->delete();

        return response()->json(['message' => 'Quiz question deleted']);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:quiz_questions,id',
        ]);

        QuizQuestion::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Quiz questions deleted']);
    }
}
