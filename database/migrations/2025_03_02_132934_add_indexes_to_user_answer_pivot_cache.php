<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_answer_pivot_cache', function (Blueprint $table) {
            $table->index(['user_id', 'question_id']);
            $table->index('answer');
        });
    }

    public function down(): void
    {
        Schema::table('user_answer_pivot_cache', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'question_id']);
            $table->dropIndex(['answer']);
        });
    }
};
