<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Models\Country;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Helper\ProgressBar;

class UserSeeder extends Seeder
{
    private const MIDDLE_EAST_COUNTRIES = [
        'Iran', 'Iraq', 'Saudi Arabia', 'Oman', 'Bahrain', 'Qatar',
        'Kuwait', 'United Arab Emirates', 'Yemen', 'Egypt', 'Jordan',
        'Syria', 'Lebanon', 'Palestine',
    ];

    public function run(): void
    {
        $faker = Faker::create();
        $countries = Country::where('access_level', '!=', 'banned')->pluck('id', 'name')->toArray();
        $middleEastCountryIds = array_filter($countries, fn($name) => in_array($name, self::MIDDLE_EAST_COUNTRIES), ARRAY_FILTER_USE_KEY);

        $bar = new ProgressBar($this->command->getOutput(), 1002);
        $bar->start();

        $maxSuperAdmins = 2;
        $maxAdmins = 5;
        $superAdminCount = 0;
        $adminCount = 0;
        $usersData = [];

        // کاربران تصادفی
        for ($i = 0; $i < 1000; $i++) {
            $gender = $faker->randomElement([Gender::Male->value, Gender::Female->value]);
            $firstName = $gender === Gender::Male->value ? $faker->firstNameMale : $faker->firstNameFemale;
            $lastName = $faker->lastName;
            $birthDate = $faker->dateTimeBetween('-70 years', '-21 years')->format('Y-m-d');
            $phoneNumber = $faker->unique()->numerify('+1###########');
            $status = $faker->randomElement([
                UserStatus::Active->value,
                UserStatus::Pending->value,
                UserStatus::Suspended->value,
                UserStatus::Blocked->value
            ]);
            $email = $faker->unique()->safeEmail;

            $role = $superAdminCount < $maxSuperAdmins
                ? UserRole::SuperAdmin->value
                : ($adminCount < $maxAdmins ? UserRole::Admin->value : UserRole::Normal->value);

            $usersData[] = [
                'first_name' => $firstName,
                'middle_name' => $faker->firstName(),
                'last_name' => $lastName,
                'display_name' => $faker->unique()->userName(),
                'gender' => $gender,
                'birth_date' => $birthDate,
                'phone_number' => $phoneNumber,
                'email' => $email,
                'password' => Hash::make($firstName . '@1234'),
                'role' => $role,
                'status' => $status,
                'father_name' => $gender === Gender::Male->value ? $faker->firstNameMale : $faker->firstNameFemale,
                'mother_name' => $faker->firstNameFemale,
                'born_country' => $faker->boolean(70) ? $faker->randomElement($middleEastCountryIds) : $faker->randomElement($countries),
                'living_country' => $faker->randomElement($countries),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($role === UserRole::SuperAdmin->value) $superAdminCount++;
            elseif ($role === UserRole::Admin->value) $adminCount++;

            $bar->advance();
        }

        // کاربر ثابت: Reza
        $usersData[] = [
            'first_name' => 'Reza',
            'middle_name' => 'BA',
            'last_name' => 'Bagheri',
            'display_name' => 'Rezo',
            'father_name' => 'Ali',
            'mother_name' => 'Fatemeh',
            'gender' => Gender::Male->value,
            'birth_date' => '1974-06-11',
            'phone_number' => '9359782272',
            'email' => 'rezabagheri@gmail.com',
            'password' => Hash::make('reza@2231353'),
            'role' => UserRole::SuperAdmin->value,
            'status' => UserStatus::Active->value,
            'born_country' => $countries['Iran'] ?? null,
            'living_country' => $countries['Armenia'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $bar->advance();

        // کاربر ثابت: Ramsin
        $usersData[] = [
            'first_name' => 'Ramsin',
            'middle_name' => 'SA',
            'last_name' => 'Savra',
            'display_name' => 'Ramsin',
            'father_name' => 'Ra',
            'mother_name' => 'Sa',
            'gender' => Gender::Male->value,
            'birth_date' => '1972-04-11',
            'phone_number' => '123456789',
            'email' => 'ramsin.savra@gmail.com',
            'password' => Hash::make('ramsin@1234'),
            'role' => UserRole::SuperAdmin->value,
            'status' => UserStatus::Active->value,
            'born_country' => $countries['Iran'] ?? null,
            'living_country' => $countries['United States'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $bar->advance();

        // درج همه‌ی کاربران به صورت یکجا
        //User::insert($usersData);
        foreach (array_chunk($usersData, 100) as $chunk) {
            User::insert($chunk);
        }

        $bar->finish();
        $this->command->info("\nUsers seeding completed!");
    }
}
