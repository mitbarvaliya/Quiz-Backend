<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\QuizScore;
use App\Models\Rank;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    public function index(Request $request)
    {
        $query = QuizAttempt::with('subjectRel.board', 'subjectRel.standard', 'chapterRel')
            ->where('user_id', $request->user()->id)
            ->orderBy('played_at', 'desc');

        return response()->json([
            'attempts' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'level' => 'required|string',
            'total_questions' => 'required|integer|min:1',
            'correct_answers' => 'required|integer|min:0',
            'time_taken' => 'required|integer|min:0',
            'details' => 'required|array|min:1',
            'details.*.question' => 'required|string',
            'details.*.options' => 'required|array|size:4',
            'details.*.correctAnswer' => 'required|integer',
            'details.*.selected' => 'nullable|string',
            'details.*.isCorrect' => 'required|boolean',
        ]);

        $attempt = QuizAttempt::create([
            'user_id' => $request->user()->id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'level' => $request->level,
            'total_questions' => $request->total_questions,
            'correct_answers' => $request->correct_answers,
            'time_taken' => $request->time_taken,
            'details' => $request->details,
            'played_at' => now(),
        ]);

        QuizScore::create([
            'user_id' => $request->user()->id,
            'points' => $request->correct_answers,
        ]);

        $subject = \App\Models\Subject::with('board', 'standard')->find($request->subject_id);

        $rank = Rank::firstOrNew([
            'user_id' => $request->user()->id,
            'subject_id' => $request->subject_id,
        ]);
        $rank->board = $subject?->board?->name;
        $rank->standard = $subject?->standard?->name;
        $rank->total_points += $request->correct_answers;
        $rank->total_questions += $request->total_questions;
        $rank->percentage = $rank->total_questions > 0
            ? round(($rank->total_points / $rank->total_questions) * 100, 2)
            : 0;
        $rank->save();

        return response()->json([
            'message' => 'Quiz attempt saved',
            'attempt' => $attempt,
            'rank' => $rank,
        ], 201);
    }

    public function show($id)
    {
        $attempt = QuizAttempt::with('subjectRel.board', 'subjectRel.standard', 'chapterRel')
            ->where('user_id', request()->user()->id)
            ->findOrFail($id);

        return response()->json([
            'attempt' => $attempt,
        ]);
    }

    public function destroy($id)
    {
        $attempt = QuizAttempt::where('user_id', request()->user()->id)
            ->findOrFail($id);
        $attempt->delete();

        return response()->json(['message' => 'Quiz attempt deleted']);
    }

    public function rank(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'level' => 'required|string',
            'correct_answers' => 'required|integer|min:0',
        ]);

        $score = $request->correct_answers;

        $higher = QuizAttempt::select('user_id')
            ->selectRaw('MAX(correct_answers) as best_score')
            ->where('subject_id', $request->subject_id)
            ->where('level', $request->level)
            ->groupBy('user_id')
            ->havingRaw('MAX(correct_answers) > ?', [$score])
            ->count();

        $total = QuizAttempt::select('user_id')
            ->where('subject_id', $request->subject_id)
            ->where('level', $request->level)
            ->distinct()
            ->count('user_id');

        $rank = $higher + 1;

        return response()->json([
            'rank' => $rank,
            'total_participants' => $total,
        ]);
    }

    public function adminResults(Request $request)
    {
        $query = QuizAttempt::query()->with('user', 'subjectRel.board', 'subjectRel.standard');

        if ($request->filled('board')) {
            $query->whereHas('subjectRel.board', function ($q) use ($request) {
                $q->where('name', $request->board);
            });
        }
        if ($request->filled('standard')) {
            $query->whereHas('subjectRel.standard', function ($q) use ($request) {
                $q->where('name', $request->standard);
            });
        }

        $results = $query->selectRaw("
                quiz_attempts.user_id,
                COUNT(*) as plays,
                AVG(correct_answers * 1.0 / NULLIF(total_questions, 0)) * 100 as avg_score,
                SUM(correct_answers) as total_correct,
                SUM(total_questions) as total_questions_sum
            ")
            ->groupBy('quiz_attempts.user_id')
            ->orderByDesc('plays')
            ->get()
            ->map(function ($item) {
                $item->avg_score = round($item->avg_score ?? 0);
                $item->name = $item->user?->name ?? 'Unknown';
                $item->email = $item->user?->email ?? '';
                $item->board = $item->user?->board ?? '';
                $item->standard = $item->user?->standard ?? '';
                unset($item->user);
                return $item;
            });

        $boards = QuizAttempt::with('subjectRel.board')->get()->pluck('board')->unique()->sort()->values();
        $standards = QuizAttempt::with('subjectRel.standard')->get()->pluck('standard')->unique()->sortBy(fn($s) => (int) (preg_match('/\d+/', $s, $m) ? $m[0] : 0))->values();

        return response()->json([
            'results' => $results,
            'boards' => $boards,
            'standards' => $standards,
        ]);
    }
}
