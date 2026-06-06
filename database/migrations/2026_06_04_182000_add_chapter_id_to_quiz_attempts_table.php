<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'chapter_id')) {
                $table->foreignId('chapter_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempts', 'chapter_id')) {
                $table->dropForeign(['chapter_id']);
                $table->dropColumn('chapter_id');
            }
        });
    }
};
