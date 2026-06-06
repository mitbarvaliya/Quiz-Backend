<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ranks');

        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('board')->nullable();
            $table->string('standard')->nullable();
            $table->integer('total_points')->default(0);
            $table->integer('total_questions')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ranks');

        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('total_points')->default(0);
            $table->integer('total_questions')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->timestamps();

            $table->unique('user_id');
        });
    }
};
