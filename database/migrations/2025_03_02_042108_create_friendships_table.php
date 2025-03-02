<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each friendship');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')
                ->comment('Foreign key referencing the user who initiated the friendship');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade')
                ->comment('Foreign key referencing the user who is the friend');
            $table->enum('status', [
                \App\Enums\FriendshipStatus::Pending->value,
                \App\Enums\FriendshipStatus::Accepted->value,
                \App\Enums\FriendshipStatus::Rejected->value
            ])->default(\App\Enums\FriendshipStatus::Pending->value)
                ->index()->comment('Status of the friendship: pending, accepted, or rejected');
            $table->timestamps();
            $table->unique(['user_id', 'target_id'])->comment('Ensures no duplicate friendships between the same users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
