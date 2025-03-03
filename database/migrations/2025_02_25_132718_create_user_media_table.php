<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_medias', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each user-media relationship');
            $table->foreignId('user_id')
                ->constrained('users', 'id', 'user_medias_user_id_foreign')
                ->onDelete('cascade')
                ->comment('Foreign key referencing the users table');
            $table->foreignId('media_id')
                ->constrained('media', 'id', 'user_medias_media_id_foreign')
                ->onDelete('cascade')
                ->comment('Foreign key referencing the media table');
            $table->unsignedTinyInteger('order')->default(0)->comment('Order of the media in the user\'s photo album');
            $table->boolean('is_profile')->default(false)->comment('Indicates if this media is the user\'s profile picture');
            $table->boolean('is_approved')->default(false)->comment('Indicates if this media has been approved');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_medias');
    }
};
