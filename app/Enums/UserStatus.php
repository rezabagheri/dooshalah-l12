<?php

namespace App\Enums;

/**
 * Enum representing the status options for users in the application.
 *
 * @package App\Enums
 */
enum UserStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Suspended = 'suspended';
    case Blocked = 'blocked';
}
