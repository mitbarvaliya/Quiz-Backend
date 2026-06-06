<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminResult;
use App\Models\Board;
use App\Models\ContactMessage;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Standard;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BoardStandardSubjectSeeder::class);
        $this->call(IndianBoardsSeeder::class);
        $this->call(UpdateStandardNamesSeeder::class);

        // Get a board, standard, subject for foreign key references
        $board = Board::first();
        $standard = Standard::first();
        $subject = Subject::first();

        // ── Admin ──
        Admin::create([
            'name' => 'Meet Barvaliya',
            'email' => 'meetbarvaliya5@gmail.com',
            'password' => 'Mit@1234',
        ]);

        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin',
        ]);

        // ── Users ──
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('demo123'),
            'age' => '18',
            'standard' => '12',
            'stream' => 'Science',
            'board' => 'CBSE',
        ]);

        $user2 = User::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'password' => bcrypt('test123'),
            'age' => '16',
            'standard' => '10',
            'stream' => null,
            'board' => 'CBSE',
        ]);

        // ── Questions (at least 2 for the subject) ──
        $q1 = Question::create([
            'board_id' => $board->id,
            'standard_id' => $standard->id,
            'subject_id' => $subject->id,
            'question' => 'What is the capital of France?',
            'option_1' => 'London',
            'option_2' => 'Paris',
            'option_3' => 'Berlin',
            'option_4' => 'Madrid',
            'correct_answer' => 2,
        ]);

        $q2 = Question::create([
            'board_id' => $board->id,
            'standard_id' => $standard->id,
            'subject_id' => $subject->id,
            'question' => 'What is 2 + 2?',
            'option_1' => '3',
            'option_2' => '4',
            'option_3' => '5',
            'option_4' => '6',
            'correct_answer' => 2,
        ]);

        // ── Quizzes ──
        $quiz = Quiz::create([
            'title' => 'General Knowledge Basics',
            'subject_id' => $subject->id,
            'level' => 'Easy',
            'status' => 'Published',
        ]);

        // ── Quiz Question pivot ──
        $quiz->questions()->attach([$q1->id, $q2->id]);

        // ── Quiz Questions (standalone table) ──
        QuizQuestion::create([
            'subject_id' => $subject->id,
            'level' => 'Easy',
            'question_id' => $q1->id,
            'question' => 'What is the capital of France?',
            'options' => ['London', 'Paris', 'Berlin', 'Madrid'],
            'correct' => 1,
        ]);

        QuizQuestion::create([
            'subject_id' => $subject->id,
            'level' => 'Easy',
            'question_id' => $q2->id,
            'question' => 'What is 2 + 2?',
            'options' => ['3', '4', '5', '6'],
            'correct' => 1,
        ]);

        // ── Quiz Attempts ──
        QuizAttempt::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'level' => 'Easy',
            'total_questions' => 2,
            'correct_answers' => 2,
            'time_taken' => 45,
            'details' => [
                ['question' => 'What is the capital of France?', 'options' => ['London', 'Paris', 'Berlin', 'Madrid'], 'correct' => 1, 'selected' => 'Paris', 'isCorrect' => true],
                ['question' => 'What is 2 + 2?', 'options' => ['3', '4', '5', '6'], 'correct' => 1, 'selected' => '4', 'isCorrect' => true],
            ],
            'played_at' => now(),
        ]);

        QuizAttempt::create([
            'user_id' => $user2->id,
            'subject_id' => $subject->id,
            'level' => 'Easy',
            'total_questions' => 2,
            'correct_answers' => 1,
            'time_taken' => 60,
            'details' => [
                ['question' => 'What is the capital of France?', 'options' => ['London', 'Paris', 'Berlin', 'Madrid'], 'correct' => 1, 'selected' => 'Paris', 'isCorrect' => true],
                ['question' => 'What is 2 + 2?', 'options' => ['3', '4', '5', '6'], 'correct' => 1, 'selected' => '5', 'isCorrect' => false],
            ],
            'played_at' => now(),
        ]);

        // ── Admin Results ──
        AdminResult::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'plays' => 1,
            'avg_score' => 100,
            'total_correct' => 2,
            'total_questions' => 2,
        ]);

        AdminResult::create([
            'user_id' => $user2->id,
            'subject_id' => $subject->id,
            'plays' => 1,
            'avg_score' => 50,
            'total_correct' => 1,
            'total_questions' => 2,
        ]);

        // ── Contact Messages ──
        ContactMessage::create([
            'name' => 'Rahul K.',
            'email' => 'rahul@example.com',
            'phone' => '9876543210',
            'message' => 'Great quizzes! Would love to see more literature topics added.',
            'is_read' => false,
        ]);

        ContactMessage::create([
            'name' => 'Sonia M.',
            'email' => 'sonia@example.com',
            'phone' => '9876543211',
            'message' => 'The timer during quizzes is too short. Could you increase it to 45 seconds?',
            'is_read' => true,
        ]);
    }
}
