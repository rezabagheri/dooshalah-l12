<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\NotificationType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each notification');
            $table->foreignId('user_id')
                ->constrained('users', 'id', 'notifications_user_id_foreign')
                ->onDelete('cascade')
                ->comment('Foreign key referencing the user receiving the notification');
            $table->foreignId('sender_id')->nullable()
                ->constrained('users', 'id', 'notifications_sender_id_foreign')
                ->onDelete('set null')
                ->comment('Foreign key referencing the user who triggered the notification');
            $table->enum('type', array_column(NotificationType::cases(), 'value'))
                ->index()->comment('Type of notification');
            $table->string('title', 100)->comment('Short title of the notification');
            $table->text('content')->comment('Content or description of the notification');
            $table->string('action_url')->nullable()->comment('URL linking to the related resource');
            $table->boolean('is_read')->default(false)->comment('Indicates if the notification has been read');
            $table->timestamp('read_at')->nullable()->comment('Timestamp when the notification was read');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID of the related resource');
            $table->string('related_type')->nullable()->comment('Type of the related resource');
            $table->json('data')->nullable()->comment('Additional metadata in JSON format');
            $table->tinyInteger('priority')->default(1)->comment('Priority level: 1 (low), 2 (medium), 3 (high)');
            $table->timestamps();
            $table->index(['user_id', 'is_read'], 'user_unread_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
