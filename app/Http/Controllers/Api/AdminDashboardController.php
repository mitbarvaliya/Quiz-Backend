<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ContactMessage;
use App\Models\Rank;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalQuizzes = Quiz::count();
        $totalPlays = QuizAttempt::count();
        $totalMessages = ContactMessage::count();
        $unreadMessages = ContactMessage::where('is_read', false)->count();

        $recentQuizzes = Quiz::with('subjectRel.board', 'subjectRel.standard')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($quiz) {
                $quiz->plays = QuizAttempt::where('subject_id', $quiz->subject_id)->count();
                return $quiz;
            });

        $topPlayers = Rank::with('user')
            ->selectRaw('user_id, SUM(total_points) as total_points, SUM(total_questions) as total_questions')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name'       => $item->user?->name ?? 'Unknown',
                    'points'     => (int) $item->total_points,
                    'questions'  => (int) $item->total_questions,
                    'percentage' => $item->total_questions > 0
                        ? round(($item->total_points / $item->total_questions) * 100)
                        : 0,
                ];
            });

        return response()->json([
            'totalUsers'      => $totalUsers,
            'totalQuizzes'    => $totalQuizzes,
            'totalPlays'      => $totalPlays,
            'totalMessages'   => $totalMessages,
            'unreadMessages'  => $unreadMessages,
            'recentQuizzes'   => $recentQuizzes,
            'topPlayers'      => $topPlayers,
        ]);
    }
}
