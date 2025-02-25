<?php

namespace Database\Seeders;

use App\Enums\CountryAccessLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Seeder for populating the countries table.
 *
 * This seeder fetches country data from the REST Countries API (https://restcountries.com)
 * and assigns access levels based on predefined rules: Middle Eastern countries are free,
 * predefined banned countries are restricted, and others require registration or are searchable only.
 *
 * @category Database
 * @package  Seeders
 */
class CountrySeeder extends Seeder
{
    /**
     * List of Middle Eastern countries that will have free access.
     *
     * @var array<string>
     */
    private const MIDDLE_EAST_COUNTRIES = [
        'Iran',
        'Iraq',
        'Saudi Arabia',
        'Oman',
        'Bahrain',
        'Qatar',
        'Kuwait',
        'United Arab Emirates',
        'Yemen',
        'Jordan',
        'Syria',
        'Lebanon',
        'Palestine',
    ];

    /**
     * List of countries that will be banned.
     *
     * @var array<string>
     */
    private const BANNED_COUNTRIES = [
        'Nigeria',
        'Kenya',
        'South Africa',
        'Algeria',
        'Morocco',
        'Ghana',
        'Egypt',
        'Tunisia',
        'Uganda',
        'Sudan',
        'Zimbabwe',
        'Cameroon',
        'Ethiopia',
        'Libya',
    ];

    /**
     * Run the database seeds.
     *
     * Fetches country data from the REST Countries API and inserts it into the countries table.
     *
     * @return void
     */
    public function run(): void
    {
        $response = Http::get('https://restcountries.com/v3.1/all?fields=name,cca2,flags');

        if ($response->failed()) {
            Log::error('Failed to fetch countries from REST Countries API: ' . $response->reason());
            throw new \RuntimeException('Could not fetch country data from API.');
        }

        $countries = $response->json();
        $dataToInsert = [];

        foreach ($countries as $country) {
            $countryName = $country['name']['common'];
            $accessLevel = $this->determineAccessLevel($countryName);
            $flagImage = $country['flags']['svg'] ?? null;

            $dataToInsert[] = [
                'name' => $countryName,
                'abbreviation' => $country['cca2'] ?? null,
                'flag_image' => $flagImage,
                'access_level' => $accessLevel,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all countries at once
        DB::table('countries')->insert($dataToInsert);
    }

    /**
     * Determine the access level for a given country.
     *
     * @param string $countryName The name of the country.
     * @return string The determined access level.
     */
    private function determineAccessLevel(string $countryName): string
    {
        if (in_array($countryName, self::MIDDLE_EAST_COUNTRIES)) {
            return CountryAccessLevel::Free->value;
        }

        if (in_array($countryName, self::BANNED_COUNTRIES)) {
            return CountryAccessLevel::Banned->value;
        }

        // Randomly split remaining countries between registration_required and searchable_only
        return rand(0, 1) === 0
            ? CountryAccessLevel::RegistrationRequired->value
            : CountryAccessLevel::SearchableOnly->value;
    }
}
