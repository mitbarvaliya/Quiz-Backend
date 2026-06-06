<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Questions table
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'board')) {
                $table->dropColumn(['board', 'standard', 'stream', 'subject']);
            }
        });
        if (!Schema::hasColumn('questions', 'subject_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            });
        }

        // Quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'board')) {
                $table->dropColumn(['board', 'standard', 'stream', 'subject']);
            }
        });
        if (!Schema::hasColumn('quizzes', 'subject_id')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            });
        }

        // Quiz questions table
        Schema::table('quiz_questions', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_questions', 'board')) {
                $table->dropColumn(['board', 'standard', 'stream', 'subject']);
            }
        });
        if (!Schema::hasColumn('quiz_questions', 'subject_id')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            });
        }

        // Quiz attempts table
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempts', 'board')) {
                $table->dropColumn(['board', 'standard', 'stream', 'subject']);
            }
        });
        if (!Schema::hasColumn('quiz_attempts', 'subject_id')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            });
        }

        // Admin results table
        if (Schema::hasColumn('admin_results', 'board')) {
            Schema::table('admin_results', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropUnique(['user_id', 'board', 'standard', 'subject']);
                $table->dropColumn(['board', 'standard', 'subject']);
            });
            Schema::table('admin_results', function (Blueprint $table) {
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
                $table->unique(['user_id', 'subject_id']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        } elseif (!Schema::hasColumn('admin_results', 'subject_id')) {
            Schema::table('admin_results', function (Blueprint $table) {
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
                $table->unique(['user_id', 'subject_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('admin_results', function (Blueprint $table) {
            if (Schema::hasColumn('admin_results', 'subject_id')) {
                $table->dropUnique(['user_id', 'subject_id']);
                $table->dropForeign(['subject_id']);
                $table->dropForeign(['user_id']);
                $table->dropColumn('subject_id');
            }
            if (!Schema::hasColumn('admin_results', 'board')) {
                $table->string('board');
                $table->string('standard');
                $table->string('subject');
                $table->unique(['user_id', 'board', 'standard', 'subject']);
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });

        foreach (['quiz_attempts', 'quiz_questions', 'quizzes', 'questions'] as $table) {
            Schema::table($table, function (Blueprint $s) use ($table) {
                if (Schema::hasColumn($table, 'subject_id')) {
                    try { $s->dropForeign(['subject_id']); } catch (\Exception $e) {}
                    $s->dropColumn('subject_id');
                }
                if (!Schema::hasColumn($table, 'board')) {
                    $s->string('board');
                    $s->string('standard');
                    $s->string('stream')->nullable();
                    $s->string('subject');
                }
            });
        }
    }
};
