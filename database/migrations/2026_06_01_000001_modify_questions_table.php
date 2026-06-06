<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('questions', 'options')) {
                $table->dropColumn('options');
            }
            if (Schema::hasColumn('questions', 'correct')) {
                $table->dropColumn('correct');
            }
        });

        if (!Schema::hasColumn('questions', 'option_1')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->text('option_1')->after('question');
                $table->text('option_2')->after('option_1');
                $table->text('option_3')->after('option_2');
                $table->text('option_4')->after('option_3');
                $table->integer('correct_answer')->after('option_4');
            });
        }

        if (!Schema::hasColumn('questions', 'board_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->foreignId('board_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('standard_id')->nullable()->constrained()->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['board_id']);
            $table->dropForeign(['standard_id']);
            $table->dropColumn(['board_id', 'standard_id', 'option_1', 'option_2', 'option_3', 'option_4', 'correct_answer']);
            $table->string('level')->default('Easy');
            $table->json('options');
            $table->integer('correct');
        });
    }
};
