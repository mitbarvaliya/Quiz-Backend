<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WeeklyLeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $perPage = $request->integer('per_page', 20);
        $page = $request->integer('page', 1);

        $query = QuizScore::select(
                'quiz_scores.user_id',
                DB::raw('SUM(quiz_scores.points) as total_points')
            )
            ->join('users', 'quiz_scores.user_id', '=', 'users.id')
            ->whereBetween('quiz_scores.created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('quiz_scores.user_id')
            ->orderByDesc('total_points');

        $totalUsers = $query->count();

        $rankings = $query->paginate($perPage, ['*'], 'page', $page);

        $rankings->getCollection()->transform(function ($item, $index) use ($rankings) {
            $item->rank = ($rankings->currentPage() - 1) * $rankings->perPage() + $index + 1;
            $user = \App\Models\User::find($item->user_id);
            $item->name = $user?->name ?? 'Unknown';
            return $item;
        });

        return response()->json([
            'leaderboard' => $rankings->items(),
            'current_page' => $rankings->currentPage(),
            'last_page' => $rankings->lastPage(),
            'per_page' => $rankings->perPage(),
            'total' => $rankings->total(),
            'week_start' => $startOfWeek->toDateString(),
            'week_end' => $endOfWeek->toDateString(),
        ]);
    }
}
