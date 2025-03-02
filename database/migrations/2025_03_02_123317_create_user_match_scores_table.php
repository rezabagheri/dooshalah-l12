<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_match_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade');
            $table->float('match_score')->comment('Percentage of match between users (0-100)');
            $table->timestamps();

            $table->unique(['user_id', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_match_scores');
    }
};
