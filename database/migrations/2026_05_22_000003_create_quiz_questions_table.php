<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('board');
            $table->string('standard');
            $table->string('stream')->nullable();
            $table->string('subject');
            $table->string('level')->default('Easy');
            $table->foreignId('question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->text('question');
            $table->json('options');
            $table->integer('correct');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
