<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_answer_pivot_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('answer'); // پاسخ به‌صورت رشته ساده
            $table->unsignedTinyInteger('weight')->default(1); // وزن سوال
            $table->timestamps();

            $table->unique(['user_id', 'question_id']); // هر کاربر فقط یه پاسخ برای هر سوال
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_answer_pivot_cache');
    }
};
