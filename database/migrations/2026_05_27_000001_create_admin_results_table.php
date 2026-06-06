<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('board');
            $table->string('standard');
            $table->string('subject');
            $table->integer('plays')->default(0);
            $table->decimal('avg_score', 5, 2)->default(0);
            $table->integer('total_correct')->default(0);
            $table->integer('total_questions')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'board', 'standard', 'subject']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_results');
    }
};
