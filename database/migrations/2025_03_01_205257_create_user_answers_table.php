<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each user answer');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')
                ->comment('Foreign key referencing the users table');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade')
                ->comment('Foreign key referencing the questions table');
            $table->json('answer')->comment('The user\'s answer stored in JSON format');
            $table->unique(['user_id', 'question_id'])->comment('Ensure one answer per user per question');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
