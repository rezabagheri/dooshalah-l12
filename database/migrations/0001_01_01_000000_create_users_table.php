<?php

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Primary key: Unique user ID');
            $table->string('first_name')->comment('First name of the user');
            $table->string('middle_name')->nullable()->comment('Middle name of the user (optional)');
            $table->string('last_name')->index()->comment('Last name of the user');
            $table->string('display_name')->unique()->index()->comment('Unique display name of the user');
            $table
                ->enum('gender', [Gender::Male->value, Gender::Female->value])
                ->default(Gender::Male->value)
                ->index()
                ->comment('Gender of the user, defaults to male');
            $table->date('birth_date')->index()->comment('Birth date of the user');
            $table->string('email')->unique()->index()->comment('Email address of the user');
            $table->string('phone_number', 20)->unique()->comment('Phone number of the user (e.g., +1234567890123)');
            $table->string('father_name', 128)->nullable()->comment('Father\'s name of the user (optional)');
            $table->string('mother_name', 128)->nullable()->comment('Mother\'s name of the user (optional)');
            $table->foreignId('born_country')->nullable()
                ->constrained('countries', 'id', 'users_born_country_foreign')
                ->onDelete('set null')
                ->comment('Country of birth');
            $table->foreignId('living_country')->nullable()
                ->constrained('countries', 'id', 'users_living_country_foreign')
                ->onDelete('set null')
                ->comment('Country of residence');
            $table->timestamp('email_verified_at')->nullable()->comment('Timestamp when email was verified');
            $table->string('password')->comment('Hashed password of the user');
            $table
                ->enum('role', [UserRole::Normal->value, UserRole::Admin->value, UserRole::SuperAdmin->value])
                ->default(UserRole::Normal->value)
                ->index()
                ->comment('User role, defaults to normal');
            $table
                ->enum('status', [UserStatus::Active->value, UserStatus::Pending->value, UserStatus::Suspended->value, UserStatus::Blocked->value])
                ->default(UserStatus::Pending->value)
                ->index()
                ->comment('User status, defaults to pending');
            $table->string('locale', 5)->nullable()->comment('User\'s preferred language (e.g., "en", "fa", "en-US")');
            $table->rememberToken()->comment('Token for remembering user login');
            $table->timestamps();
            $table->softDeletes()->comment('Timestamp for soft deletion');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary()->comment('Email address associated with the reset token');
            $table->string('token')->comment('Reset token value');
            $table->timestamp('created_at')->nullable()->comment('Timestamp when the token was created');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment('Unique session ID');
            $table->foreignId('user_id')->nullable()
                ->constrained('users', 'id', 'sessions_user_id_foreign')
                ->onDelete('cascade')
                ->index()
                ->comment('Foreign key to users table');
            $table->string('ip_address', 45)->nullable()->comment('IP address of the session');
            $table->text('user_agent')->nullable()->comment('User agent string of the session');
            $table->longText('payload')->comment('Session data payload');
            $table->integer('last_activity')->index()->comment('Timestamp of last session activity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
