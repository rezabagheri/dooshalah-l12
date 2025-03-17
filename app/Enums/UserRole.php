<?php

namespace App\Enums;

enum UserRole: string
{
    case Normal = 'normal';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal User',
            self::Admin => 'Admin',
            self::SuperAdmin => 'Super Admin',
        };
    }
}
