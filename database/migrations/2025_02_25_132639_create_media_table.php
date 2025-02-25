<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the media table.
 *
 * This migration sets up the 'media' table to store files such as images, videos, and audio.
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
        Schema::create('media', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique identifier for each media');
            $table->string('path')->comment('Path to the media file in the system');
            $table->string('original_name')->comment('Original name of the uploaded file (e.g., "profile.jpg")');
            $table->enum('type', ['image', 'video', 'audio'])->default('image')->index()->comment('Type of media: image, video, or audio');
            $table->string('mime_type')->comment('MIME type of the media file (e.g., image/jpeg)');
            $table->unsignedBigInteger('size')->comment('Size of the media file in bytes');
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
        Schema::dropIfExists('media');
    }
};
