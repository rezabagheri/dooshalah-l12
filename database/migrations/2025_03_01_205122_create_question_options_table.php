<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each option');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade')
                ->comment('Foreign key referencing the questions table');
            $table->string('option_value')->comment('The value or text of the option');
            $table->string('option_key')->nullable()->comment('Short key for the option (e.g., "pizza" for "Pizza")');
            $table->unsignedInteger('order_in_question')->nullable()->comment('Order of the option in the question');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
