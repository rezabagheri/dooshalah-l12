<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each block relationship');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')
                ->comment('Foreign key referencing the user who initiated the block');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade')
                ->comment('Foreign key referencing the user who is blocked');
            $table->timestamps();
            $table->unique(['user_id', 'target_id'])->comment('Ensures a user can block another user only once');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
