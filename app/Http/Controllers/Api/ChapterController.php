<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::with('subject.board', 'subject.standard')
            ->orderBy('subject_id')
            ->orderBy('name')
            ->get();

        return response()->json(['chapters' => $chapters]);
    }

    public function bySubject(Subject $subject)
    {
        $subject->load('board', 'standard');
        $chapters = $subject->chapters()->orderBy('name')->get();

        return response()->json(['chapters' => $chapters, 'subject' => $subject]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $chapter = Chapter::create($data);
        $chapter->load('subject.board', 'subject.standard');

        return response()->json(['chapter' => $chapter], 201);
    }

    public function update(Request $request, Chapter $chapter)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $chapter->update($data);
        $chapter->load('subject.board', 'subject.standard');

        return response()->json(['chapter' => $chapter]);
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();
        return response()->json(['message' => 'Chapter deleted']);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:chapters,id',
        ]);
        Chapter::whereIn('id', $ids['ids'])->delete();
        return response()->json(['message' => 'Chapters deleted']);
    }
}
