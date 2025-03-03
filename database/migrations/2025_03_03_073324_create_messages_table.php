<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MessageStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each message');
            $table->foreignId('sender_id')->nullable()
                ->constrained('users', 'id', 'messages_sender_id_foreign')
                ->onDelete('set null')
                ->comment('Foreign key referencing the user who sent the message');
            $table->foreignId('receiver_id')->nullable()
                ->constrained('users', 'id', 'messages_receiver_id_foreign')
                ->onDelete('set null')
                ->comment('Foreign key referencing the user who received the message');
            $table->string('subject', 255)->nullable()->comment('Subject of the message');
            $table->text('message')->comment('Content of the message');
            $table->timestamp('sent_at')->nullable()->comment('Timestamp when the message was sent');
            $table->timestamp('read_at')->nullable()->comment('Timestamp when the message was read');
            $table->foreignId('parent_id')->nullable()
                ->constrained('messages', 'id', 'messages_parent_id_foreign')
                ->onDelete('cascade')
                ->comment('Foreign key referencing the parent message for threading');
            $table->enum('status', array_column(MessageStatus::cases(), 'value'))
                ->default(MessageStatus::Draft->value)
                ->comment('Status of the message: draft or sent');
            $table->boolean('is_deleted')->default(false)->comment('Indicates if the message is deleted');
            $table->timestamps();
            $table->index(['sender_id', 'receiver_id'], 'sender_receiver_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
