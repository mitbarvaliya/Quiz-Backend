<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Standard;
use App\Models\Subject;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    private function resolveSubjectId($boardName, $standardName, $subjectName, $stream = null)
    {
        $board = Board::where('name', $boardName)->firstOrFail();
        $standard = Standard::where('board_id', $board->id)
            ->where('name', $standardName)
            ->firstOrFail();
        $subjectModel = Subject::where('board_id', $board->id)
            ->where('standard_id', $standard->id)
            ->where('name', $subjectName)
            ->where('stream', $stream)
            ->firstOrFail();
        return $subjectModel->id;
    }

    private function resolveBoardId($boardName)
    {
        return Board::where('name', $boardName)->firstOrFail()->id;
    }

    private function resolveStandardId($boardId, $standardName)
    {
        return Standard::where('board_id', $boardId)
            ->where('name', $standardName)
            ->firstOrFail()->id;
    }

    public function store(Request $request)
    {
        $request->validate([
            'board_id' => 'required|exists:boards,id',
            'standard_id' => 'required|exists:standards,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'question' => 'required|string',
            'option_1' => 'required|string',
            'option_2' => 'required|string',
            'option_3' => 'required|string',
            'option_4' => 'required|string',
            'correct_answer' => 'required|integer|between:1,4',
        ]);

        $question = Question::create([
            'board_id' => $request->board_id,
            'standard_id' => $request->standard_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'question' => $request->question,
            'option_1' => $request->option_1,
            'option_2' => $request->option_2,
            'option_3' => $request->option_3,
            'option_4' => $request->option_4,
            'correct_answer' => $request->correct_answer,
        ]);

        $question->load('boardRel', 'standardRel', 'subjectRel.board', 'subjectRel.standard', 'chapterRel');

        return response()->json([
            'message' => 'Question created successfully',
            'question' => $question,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $question = Question::with('boardRel', 'standardRel', 'subjectRel.board', 'subjectRel.standard', 'chapterRel')->findOrFail($id);

        $rules = [
            'chapter_id' => 'nullable|exists:chapters,id',
            'question' => 'sometimes|required|string',
            'option_1' => 'sometimes|required|string',
            'option_2' => 'sometimes|required|string',
            'option_3' => 'sometimes|required|string',
            'option_4' => 'sometimes|required|string',
            'correct_answer' => 'sometimes|required|integer|between:1,4',
        ];

        if ($request->has('board_id')) {
            $rules['board_id'] = 'required|exists:boards,id';
            $rules['standard_id'] = 'required|exists:standards,id';
            $rules['subject_id'] = 'required|exists:subjects,id';
        } elseif ($request->has('board')) {
            $rules['board'] = 'required|string|max:255';
            $rules['standard'] = 'required|string|max:255';
            $rules['subject'] = 'required|string|max:255';
            $rules['stream'] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        $data = $request->only(['question', 'option_1', 'option_2', 'option_3', 'option_4', 'correct_answer', 'chapter_id']);

        if ($request->has('board_id')) {
            $data['board_id'] = $request->board_id;
            $data['standard_id'] = $request->standard_id;
            $data['subject_id'] = $request->subject_id;
        } elseif ($request->has('board')) {
            $board = Board::where('name', $request->board)->firstOrFail();
            $data['board_id'] = $board->id;
            $standard = Standard::where('board_id', $board->id)
                ->where('name', $request->standard)
                ->firstOrFail();
            $data['standard_id'] = $standard->id;
            $data['subject_id'] = $this->resolveSubjectId(
                $request->board,
                $request->standard,
                $request->subject,
                $request->stream
            );
        }

        $question->update($data);
        $question->load('boardRel', 'standardRel', 'subjectRel.board', 'subjectRel.standard', 'chapterRel');

        return response()->json([
            'message' => 'Question updated successfully',
            'question' => $question,
        ]);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        return response()->json(['message' => 'Question deleted']);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:questions,id',
        ]);

        Question::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Questions deleted successfully']);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'board_id' => 'required|exists:boards,id',
            'standard_id' => 'required|exists:standards,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.option_1' => 'required|string',
            'questions.*.option_2' => 'required|string',
            'questions.*.option_3' => 'required|string',
            'questions.*.option_4' => 'required|string',
            'questions.*.correct_answer' => 'required|integer|between:1,4',
        ]);

        $insertData = array_map(function ($q) use ($request) {
            return [
                'board_id' => $request->board_id,
                'standard_id' => $request->standard_id,
                'subject_id' => $request->subject_id,
                'chapter_id' => $request->chapter_id,
                'question' => $q['question'],
                'option_1' => $q['option_1'],
                'option_2' => $q['option_2'],
                'option_3' => $q['option_3'],
                'option_4' => $q['option_4'],
                'correct_answer' => $q['correct_answer'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $request->questions);

        Question::insert($insertData);

        $questions = Question::with('boardRel', 'standardRel', 'subjectRel.board', 'subjectRel.standard', 'chapterRel')
            ->orderBy('created_at', 'desc')
            ->take(count($insertData))
            ->get();

        return response()->json([
            'message' => count($insertData) . ' questions created successfully',
            'questions' => $questions,
        ], 201);
    }

    public function index(Request $request)
    {
        if ($request->filled('level') && $request->filled('subject_id')) {
            $quizIds = Quiz::where('subject_id', $request->subject_id)
                ->where('level', $request->level)
                ->where('status', 'Published')
                ->pluck('id');

            $questionIds = \DB::table('quiz_question')
                ->whereIn('quiz_id', $quizIds)
                ->pluck('question_id');

            $questions = Question::with('boardRel', 'standardRel', 'subjectRel.board', 'subjectRel.standard', 'chapterRel')
                ->whereIn('id', $questionIds)
                ->get()
                ->shuffle();

            return response()->json(['questions' => $questions]);
        }

        $query = Question::with('boardRel', 'standardRel', 'subjectRel.board', 'subjectRel.standard', 'chapterRel');

        if ($request->filled('board')) {
            $query->whereHas('boardRel', function ($q) use ($request) {
                $q->where('name', $request->board);
            });
        }
        if ($request->filled('standard')) {
            $query->whereHas('standardRel', function ($q) use ($request) {
                $q->where('name', $request->standard);
            });
        }
        if ($request->filled('stream')) {
            $query->whereHas('subjectRel', function ($q) use ($request) {
                $q->where('stream', $request->stream);
            });
        }
        if ($request->filled('subject')) {
            $query->whereHas('subjectRel', function ($q) use ($request) {
                $q->where('name', $request->subject);
            });
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('board_id')) {
            $query->where('board_id', $request->board_id);
        }
        if ($request->filled('standard_id')) {
            $query->where('standard_id', $request->standard_id);
        }
        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->chapter_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'questions' => $query->orderBy('created_at', 'desc')->get(),
        ]);
    }

    // ===================== QUIZ =====================

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'chapter_ids' => 'nullable|array',
            'chapter_ids.*' => 'integer|exists:chapters,id',
            'level' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:Published,Draft',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id ?? ($request->chapter_ids ? $request->chapter_ids[0] : null),
            'level' => $request->level ?? 'Medium',
            'status' => $request->status ?? 'Published',
        ]);

        $quiz->questions()->attach($request->question_ids);

        $quiz->load('subjectRel.board', 'subjectRel.standard', 'chapterRel');
        $quiz->loadCount('questions');

        return response()->json([
            'message' => 'Quiz created successfully',
            'quiz' => $quiz,
        ], 201);
    }

    public function getQuizzes(Request $request)
    {
        $query = Quiz::with('subjectRel.board', 'subjectRel.standard', 'chapterRel')->withCount('questions');

        if ($request->query('all') !== 'true') {
            $query->where('status', 'Published');
        }

        $quizzes = $query->orderBy('created_at', 'desc')->get();

        $quizzes->each(function ($quiz) {
            $quiz->plays = QuizAttempt::where('subject_id', $quiz->subject_id)
                ->count();
        });

        return response()->json(['quizzes' => $quizzes]);
    }

    public function getQuiz($id)
    {
        $quiz = Quiz::with('questions.boardRel', 'questions.standardRel', 'questions.subjectRel.board', 'questions.subjectRel.standard', 'questions.chapterRel')->withCount('questions')->findOrFail($id);
        return response()->json(['quiz' => $quiz]);
    }

    public function updateQuiz(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'chapter_ids' => 'nullable|array',
            'chapter_ids.*' => 'integer|exists:chapters,id',
            'level' => 'sometimes|nullable|string|max:50',
            'status' => 'sometimes|nullable|string|in:Published,Draft',
            'question_ids' => 'sometimes|required|array|min:1',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);

        $data = $request->only(['title', 'subject_id', 'chapter_id', 'level', 'status']);
        if ($request->has('chapter_ids')) {
            $data['chapter_id'] = $request->chapter_ids[0] ?? null;
        }
        $quiz->update(array_filter($data, fn($v) => $v !== null));

        if ($request->has('question_ids')) {
            $quiz->questions()->sync($request->question_ids);
        }

        $quiz->load('subjectRel.board', 'subjectRel.standard', 'chapterRel');
        $quiz->loadCount('questions');

        return response()->json([
            'message' => 'Quiz updated successfully',
            'quiz' => $quiz,
        ]);
    }

    public function deleteQuiz($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();
        return response()->json(['message' => 'Quiz deleted']);
    }

    public function detachQuizQuestions(Request $request, $id)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);
        $quiz = Quiz::findOrFail($id);
        $quiz->questions()->detach($request->question_ids);
        return response()->json(['message' => 'Questions removed from quiz']);
    }

    public function updateQuizStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Published,Draft',
        ]);

        $quiz = Quiz::findOrFail($id);
        $quiz->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Quiz status updated',
            'quiz' => $quiz,
        ]);
    }

    public function getOptions(Request $request)
    {
        if ($request->filled('board')) {
            $boardModel = Board::where('name', $request->board)->first();
            if (!$boardModel) {
                return response()->json(['standards' => []]);
            }
            $standards = Standard::where('board_id', $boardModel->id)
                ->get()
                ->sortBy(fn($s) => (int) (preg_match('/\d+/', $s->name, $m) ? $m[0] : 0))
                ->pluck('name')
                ->values();
            return response()->json(['standards' => $standards]);
        }

        $boards = Board::orderBy('name')->pluck('name');

        return response()->json([
            'boards' => $boards,
        ]);
    }

    public function getQuizzesByFilter(Request $request)
    {
        $query = Quiz::with('subjectRel.board', 'subjectRel.standard', 'chapterRel')
            ->withCount('questions')
            ->where('status', 'Published');

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
        if ($request->filled('stream')) {
            $query->whereHas('subjectRel', function ($q) use ($request) {
                $q->where('stream', $request->stream);
            });
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $quizzes = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['quizzes' => $quizzes]);
    }
}
