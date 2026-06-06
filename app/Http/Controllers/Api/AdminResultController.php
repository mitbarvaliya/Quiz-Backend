<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use Illuminate\Http\Request;

class AdminResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Rank::query()->with('user', 'subjectRel');

        if ($request->filled('board')) {
            $query->where('board', $request->board);
        }
        if ($request->filled('standard')) {
            $query->where('standard', $request->standard);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $results = $query->orderBy('percentage', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'user_id'           => $item->user_id,
                    'name'              => $item->user?->name ?? 'Unknown',
                    'board'             => $item->board ?? '',
                    'standard'          => $item->standard ?? '',
                    'subject'           => $item->subjectRel?->name ?? '',
                    'total_points'      => $item->total_points,
                    'total_questions'   => $item->total_questions,
                    'score_percentage'  => $item->percentage,
                ];
            })
            ->values();

        $boards = Rank::select('board')->distinct()->whereNotNull('board')->orderBy('board')->pluck('board');
        $standards = collect();
        if ($request->filled('board')) {
            $standards = Rank::where('board', $request->board)
                ->select('standard')->distinct()->whereNotNull('standard')
                ->get()->pluck('standard')->sortBy(fn($s) => (int) (preg_match('/\d+/', $s, $m) ? $m[0] : 0))->values();
        }
        $subjects = collect();
        if ($request->filled('standard')) {
            $subjectIds = Rank::where('board', $request->board)
                ->where('standard', $request->standard)
                ->whereNotNull('subject_id')
                ->select('subject_id')->distinct()->pluck('subject_id');
            $subjects = \App\Models\Subject::whereIn('id', $subjectIds)->pluck('name', 'id');
        }

        return response()->json([
            'results'    => $results,
            'boards'     => $boards,
            'standards'  => $standards,
            'subjects'   => $subjects,
        ]);
    }
}
