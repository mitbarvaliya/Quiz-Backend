<?php

namespace App\Console\Commands;

use App\Models\AdminResult;
use App\Models\QuizAttempt;
use Illuminate\Console\Command;

class PopulateAdminResults extends Command
{
    protected $signature = 'admin:populate-results';
    protected $description = 'Populate admin_results table from quiz_attempts';

    public function handle()
    {
        $this->info('Populating admin_results...');

        AdminResult::truncate();

        $aggregates = QuizAttempt::selectRaw("
                user_id,
                board,
                standard,
                subject,
                COUNT(*) as plays,
                AVG(correct_answers * 1.0 / NULLIF(total_questions, 0)) * 100 as avg_score,
                SUM(correct_answers) as total_correct,
                SUM(total_questions) as total_questions
            ")
            ->groupBy('user_id', 'board', 'standard', 'subject')
            ->get();

        $bar = $this->output->createProgressBar($aggregates->count());

        foreach ($aggregates as $row) {
            AdminResult::create([
                'user_id'         => $row->user_id,
                'board'           => $row->board,
                'standard'        => $row->standard,
                'subject'         => $row->subject,
                'plays'           => $row->plays,
                'avg_score'       => round($row->avg_score ?? 0, 2),
                'total_correct'   => $row->total_correct,
                'total_questions' => $row->total_questions,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done! ' . $aggregates->count() . ' records inserted.');
    }
}
