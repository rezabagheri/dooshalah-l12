<?php

namespace App\Enums;

/**
 * Enum representing the role levels for users in the application.
 *
 * @package App\Enums
 */
enum UserRole: string
{
    case Normal = 'normal';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';
}
