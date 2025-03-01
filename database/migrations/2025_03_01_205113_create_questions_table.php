<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each question');
            $table->unsignedInteger('page')->comment('Page number in the wizard');
            $table->unsignedInteger('order_in_page')->comment('Order of the question in the page');
            $table->string('question')->unique()->comment('The text of the question');
            $table->string('answer_type')->index();
            $table->string('category')->nullable()->comment('Category of the question (e.g., personal, interests)');
            $table->string('search_label')->nullable()->unique()->comment('Label used for searching in forms');
            $table->string('answer_label')->nullable()->unique()->comment('Label used to display the answer on the profile page');
            $table->boolean('is_required')->default(false)->comment('Indicates if the question is mandatory');
            $table->boolean('is_editable')->default(true)->comment('Indicates if the answer can be edited later');
            $table->boolean('is_visible')->default(true)->comment('Indicates if the answer is visible on the profile');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
