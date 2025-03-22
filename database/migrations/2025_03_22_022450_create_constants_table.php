<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the 'constants' table to store application constants.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('constants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Unique name of the constant (e.g., "Site Logo")');
            $table->string('type')->comment('Data type of the constant: string, text, url, email, html, image, number, boolean');
            $table->text('value')->nullable()->comment('Current value of the constant (e.g., file path for images)');
            $table->text('default_value')->nullable()->comment('Default value if the current value is not set');
            $table->boolean('is_required')->default(false)->comment('Indicates if the constant is required and cannot be deleted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'constants' table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('constants');
    }
};
