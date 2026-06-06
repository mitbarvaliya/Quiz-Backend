<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('quizAttempts')->get();

        $data = $users->map(function ($user) {
            $attempts = $user->quizAttempts;
            $plays = $attempts->count();
            $score = $plays > 0
                ? round($attempts->avg(fn($a) => $a->total_questions > 0 ? ($a->correct_answers / $a->total_questions) * 100 : 0))
                : 0;

            return [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'plays'      => $plays,
                'score'      => $score,
                'joined'     => $user->created_at ? $user->created_at->format('M Y') : '',
                'active'     => true,
            ];
        })->sortByDesc('plays')->values();

        return response()->json(['users' => $data]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->quizAttempts()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
