<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('age')->nullable()->after('email');
            $table->string('standard')->nullable()->after('age');
            $table->string('stream')->nullable()->after('standard');
            $table->string('board')->nullable()->after('stream');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age', 'standard', 'stream', 'board']);
        });
    }
};
