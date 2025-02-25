<?php
namespace App\Enums;
/**
 * Enum representing the access levels for countries in the application.
 *
 * @package App\Enums
 */
enum CountryAccessLevel: string
{
    case Free = 'free';
    case RegistrationRequired = 'registration_required';
    case Banned = 'banned';
    case SearchableOnly = 'searchable_only';
}
