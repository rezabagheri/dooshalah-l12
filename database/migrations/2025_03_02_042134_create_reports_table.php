<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each report');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')
                ->comment('Foreign key referencing the user who submitted the report');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade')
                ->comment('Foreign key referencing the user being reported');
            $table->text('report')->comment('Details of the report submitted by the user');
            $table->text('answer')->nullable()->comment('Response to the report (if provided)');
            $table->string('page_url')->nullable()->comment('URL of the page where the report was made');
            $table->string('user_agent')->nullable()->comment('User agent of the reporting user\'s device');
            $table->timestamp('review_at')->nullable()->comment('Timestamp when the report was reviewed');
            $table->enum('status', [
                \App\Enums\FriendshipStatus::Pending->value,
                \App\Enums\FriendshipStatus::Accepted->value,
                \App\Enums\FriendshipStatus::Rejected->value
            ])->default(\App\Enums\FriendshipStatus::Pending->value)
                ->index()->comment('Status of the report: pending, accepted, or rejected');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
