<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Name of the menu (e.g., sidebar-menu, top-menu)');
            $table->string('slug')->unique()->comment('Unique identifier for the menu (e.g., sidebar, top)');
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade')->comment('The menu this item belongs to');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade')->comment('Parent item for submenus (null if top-level)');
            $table->string('label')->comment('Display text for the menu item');
            $table->string('route')->comment('Route or URL for the menu item');
            $table->string('icon')->nullable()->comment('Icon class for the menu item (e.g., bi bi-speedometer)');
            $table->unsignedTinyInteger('order')->default(0)->comment('Order of the item in the menu');
            $table->boolean('has_divider')->default(false)->comment('Whether to add a divider after this item');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
