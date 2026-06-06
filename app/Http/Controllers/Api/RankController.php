<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Rank;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankController extends Controller
{
    public function top(Request $request)
    {
        $query = Rank::query()->with('user');

        if ($request->filled('board')) {
            $query->where('board', $request->board);
        }
        if ($request->filled('standard')) {
            $query->where('standard', $request->standard);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $rankings = $query
            ->orderByDesc('percentage')
            ->orderByDesc('total_points')
            ->limit(20)
            ->get()
            ->map(function ($item, $index) {
                $item->rank = $index + 1;
                $item->avg_score = round($item->percentage ?? 0);
                $item->name = $item->user?->name ?? 'Unknown';
                $item->email = $item->user?->email ?? '';
                $item->board = $item->board ?? '';
                $item->standard = $item->standard ?? '';
                $item->stream = $item->user?->stream ?? '';
                $item->plays = 0;
                $item->total_correct = $item->total_points;
                $item->total_questions_sum = $item->total_questions;
                unset($item->user);
                return $item;
            });

        $boards = Board::orderBy('name')->pluck('name');
        $standards = Standard::get()->sortBy(fn($s) => (int) (preg_match('/\d+/', $s->name, $m) ? $m[0] : 0))->pluck('name')->values();

        return response()->json([
            'rankings' => $rankings,
            'boards' => $boards,
            'standards' => $standards,
        ]);
    }

    public function myRank(Request $request)
    {
        $user = $request->user();

        $myRanks = Rank::where('user_id', $user->id)->get();
        $totalPoints = $myRanks->sum('total_points');
        $totalQuestions = $myRanks->sum('total_questions');
        $percentage = $totalQuestions > 0 ? round(($totalPoints / $totalQuestions) * 100) : 0;

        $allUsers = Rank::select('user_id')
            ->selectRaw('SUM(total_points) as tp')
            ->selectRaw('SUM(total_questions) as tq')
            ->groupBy('user_id')
            ->get();

        $totalUsers = $allUsers->where('tq', '>', 0)->count();
        $betterCount = $allUsers
            ->where('tq', '>', 0)
            ->filter(function ($u) use ($totalPoints, $totalQuestions, $user) {
                $uPct = $u->tq > 0 ? ($u->tp / $u->tq) * 100 : 0;
                $myPct = $totalQuestions > 0 ? ($totalPoints / $totalQuestions) * 100 : 0;
                if ($uPct > $myPct) return true;
                if ($uPct == $myPct && $u->tp > $totalPoints && $u->user_id != $user->id) return true;
                return false;
            })
            ->count();

        $rankPosition = $betterCount + 1;

        return response()->json([
            'rank' => [
                'user_id'         => $user->id,
                'name'            => $user->name,
                'total_points'    => $totalPoints,
                'total_questions' => $totalQuestions,
                'percentage'      => $percentage,
                'position'        => $rankPosition,
                'total_users'     => $totalUsers,
            ],
        ]);
    }
}
