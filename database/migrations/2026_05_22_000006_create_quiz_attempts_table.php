<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('board');
            $table->string('standard');
            $table->string('stream')->nullable();
            $table->string('subject');
            $table->string('level');
            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->integer('time_taken');
            $table->timestamp('played_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
