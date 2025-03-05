<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // فرستنده
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); // گیرنده
            $table->text('content'); // محتوای پیام
            $table->boolean('is_read')->default(false); // آیا پیام خونده شده
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
