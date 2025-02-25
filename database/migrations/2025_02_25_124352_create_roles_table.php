<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the roles and role_user tables.
 *
 * This migration sets up the 'roles' table to store user roles and the 'role_user'
 * pivot table to associate users with their roles in the application.
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
     * Creates the 'roles' table with necessary columns and relationships.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'roles' table
        Schema::create('roles', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique role ID');
            $table->string('name')->unique()->comment('Role name (e.g., normal, admin, super_admin)');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'role_user' and 'roles' tables in the correct order to respect foreign key constraints.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
