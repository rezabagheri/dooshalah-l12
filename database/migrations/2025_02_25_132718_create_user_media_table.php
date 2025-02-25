<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the user_medias table.
 *
 * This migration sets up the 'user_medias' table to associate users with their media files,
 * including profile pictures and photo albums.
 *
 * @category Database
 * @package  Migrations
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://paradisecyber.com
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_medias', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each user-media relationship');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index()->comment('Foreign key referencing the users table');
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade')->comment('Foreign key referencing the media table');
            $table->unsignedTinyInteger('order')->default(0)->comment('Order of the media in the user\'s photo album');
            $table->boolean('is_profile')->default(false)->comment('Indicates if this media is the user\'s profile picture');
            $table->boolean('is_approved')->default(false)->comment('Indicates if this media has been approved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_medias');
    }
};
