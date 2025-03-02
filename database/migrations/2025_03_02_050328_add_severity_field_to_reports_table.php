<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('severity', [
                \App\Enums\Severity::Low->value,
                \App\Enums\Severity::Medium->value,
                \App\Enums\Severity::High->value
            ])->default(\App\Enums\Severity::Medium->value)
                ->comment('Severity of the report: low, medium, or high')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium')->change();
        });
    }
};
