<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstantsSeeder extends Seeder
{
    /**
     * Seed the constants table with initial required constants.
     *
     * @return void
     */
    public function run(): void
    {
        $constants = [
            [
                'name' => 'Site Logo',
                'type' => 'image',
                'value' => '/default-logo.png',
                'default_value' => '/default-logo.png',
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'App Name',
                'type' => 'string',
                'value' => 'MyApp',
                'default_value' => 'MyApp',
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin Info Email',
                'type' => 'email',
                'value' => 'info@example.com',
                'default_value' => 'info@example.com',
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('constants')->insert($constants);
    }
}
