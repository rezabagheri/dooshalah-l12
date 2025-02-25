<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the countries table.
 *
 * This migration defines the structure for storing country data, including
 * name, abbreviation, flag image, and access levels.
 *
 * @category Database
 * @package  Migrations
 * @author   Reza Bagheri <rezabagheri@gmail.com>
 * @license  MIT License
 * @link     https://glenar.com
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method creates the 'countries' table with the necessary columns and indexes.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique country ID');
            $table->string('name')->unique()->comment('Full name of the country (unique)');
            $table->string('abbreviation', 3)->nullable()->comment('ISO 3166-1 country code (e.g., US, IR)');
            $table->string('flag_image')->nullable()->comment('Path to the country flag image');
            $table->timestamps();
            $table->enum('access_level', ['free', 'banned', 'registration_required', 'searchable_only'])
                ->default('free')
                ->comment('Access level for the country (free, banned, requires registration, searchable only)');

            // Indexes for better search performance
            $table->index('name', 'countries_name_index');
            $table->index('abbreviation', 'countries_abbreviation_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method drops the 'countries' table if it exists.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
