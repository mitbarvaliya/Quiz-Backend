<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Standard;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectManagementController extends Controller
{
    // ===================== BOARDS =====================

    public function boards()
    {
        $query = Board::orderBy('name');
        if (request('has_standards')) {
            $query->whereHas('standards');
        }
        if (request('has_subjects')) {
            $query->whereHas('standards.subjects');
        }
        if (request('has_questions')) {
            $query->whereHas('standards.subjects.questions');
        }
        if (request('has_quizzes')) {
            $query->whereHas('standards.subjects.quizzes');
        }
        if (request('has_chapters')) {
            $query->whereHas('standards.subjects.chapters');
        }
        return response()->json(['boards' => $query->get()]);
    }

    public function storeBoard(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:boards,name',
        ]);

        $board = Board::create($data);

        return response()->json(['board' => $board], 201);
    }

    public function updateBoard(Request $request, Board $board)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:boards,name,' . $board->id,
        ]);

        $board->update($data);

        return response()->json(['board' => $board]);
    }

    public function destroyBoard(Board $board)
    {
        $board->delete();
        return response()->json(['message' => 'Board deleted']);
    }

    public function bulkDeleteBoards(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:boards,id']);
        Board::whereIn('id', $ids['ids'])->delete();
        return response()->json(['message' => 'Boards deleted']);
    }

    // ===================== STANDARDS =====================

    public function standards(Board $board)
    {
        $query = $board->standards();
        if (request('has_questions')) {
            $query->whereHas('subjects.questions');
        }
        if (request('has_quizzes')) {
            $query->whereHas('subjects.quizzes');
        }
        if (request('has_chapters')) {
            $query->whereHas('subjects.chapters');
        }
        $standards = $query->get()->sortBy(fn($s) => (int) (preg_match('/\d+/', $s->name, $m) ? $m[0] : 0))->values();
        return response()->json(['standards' => $standards]);
    }

    public function allStandards()
    {
        $standards = Standard::with('board')->orderBy('board_id')->get()
            ->sortBy(fn($s) => (int) (preg_match('/\d+/', $s->name, $m) ? $m[0] : 0))
            ->values();
        return response()->json(['standards' => $standards]);
    }

    public function storeStandard(Request $request)
    {
        $data = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'name' => 'required|string|max:255',
        ]);

        $standard = Standard::create($data);

        return response()->json(['standard' => $standard], 201);
    }

    public function updateStandard(Request $request, Standard $standard)
    {
        $data = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'name' => 'required|string|max:255',
        ]);

        $standard->update($data);

        return response()->json(['standard' => $standard]);
    }

    public function destroyStandard(Standard $standard)
    {
        $standard->delete();
        return response()->json(['message' => 'Standard deleted']);
    }

    public function bulkDeleteStandards(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:standards,id']);
        Standard::whereIn('id', $ids['ids'])->delete();
        return response()->json(['message' => 'Standards deleted']);
    }

    // ===================== SUBJECTS (by standard) =====================

    public function subjectsByStandard($boardId, $standardId)
    {
        $standard = Standard::findOrFail($standardId);
        $query = $standard->subjects()->orderBy('name');
        if (request('has_questions')) {
            $query->whereHas('questions');
        }
        return response()->json(['subjects' => $query->get()]);
    }

    public function subjectsForStandard($standardId)
    {
        $standard = Standard::findOrFail($standardId);
        $query = $standard->subjects()->orderBy('name');
        if (request('has_questions')) {
            $query->whereHas('questions');
        }
        if (request('has_chapters')) {
            $query->whereHas('chapters');
        }
        return response()->json(['subjects' => $query->get()]);
    }

    // ===================== SUBJECTS CRUD =====================

    public function index()
    {
        $subjects = Subject::with(['board', 'standard'])->orderBy('board_id')->orderBy('standard_id')->get();
        return response()->json(['subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'board' => 'required|string|max:255',
            'standard' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'stream' => 'nullable|string|max:255',
        ]);

        $board = Board::firstOrCreate(['name' => $data['board']]);
        $standard = Standard::firstOrCreate([
            'board_id' => $board->id,
            'name' => $data['standard'],
        ]);

        $subject = Subject::create([
            'board_id' => $board->id,
            'standard_id' => $standard->id,
            'name' => $data['name'],
            'stream' => $data['stream'] ?? null,
        ]);
        $subject->load(['board', 'standard']);

        return response()->json(['subject' => $subject], 201);
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'board' => 'required|string|max:255',
            'standard' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'stream' => 'nullable|string|max:255',
        ]);

        $board = Board::firstOrCreate(['name' => $data['board']]);
        $standard = Standard::firstOrCreate([
            'board_id' => $board->id,
            'name' => $data['standard'],
        ]);

        $subject->update([
            'board_id' => $board->id,
            'standard_id' => $standard->id,
            'name' => $data['name'],
            'stream' => $data['stream'] ?? null,
        ]);
        $subject->load(['board', 'standard']);

        return response()->json(['subject' => $subject]);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Subject deleted']);
    }

    public function bulkDeleteSubjects(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:subjects,id']);
        Subject::whereIn('id', $ids['ids'])->delete();
        return response()->json(['message' => 'Subjects deleted']);
    }

    // ===================== NESTED SUBJECTS (for frontend) =====================

    public function nested()
    {
        $boards = Board::with('standards.subjects')->orderBy('name')->get();

        $result = [];
        foreach ($boards as $board) {
            $boardData = [];
            foreach ($board->standards as $standard) {
                $subjects = $standard->subjects->where('stream', null)->pluck('name')->values()->toArray();
                $boardData[$standard->name] = $subjects;

                $streamSubjects = $standard->subjects->where('stream', '!=', null);
                foreach ($streamSubjects as $s) {
                    $boardData['streams'][$s->stream][$standard->name][] = $s->name;
                }
            }
            $result[$board->name] = $boardData;
        }

        return response()->json(['subjects' => $result]);
    }
}
