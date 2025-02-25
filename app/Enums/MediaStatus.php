<?php

namespace App\Enums;

/**
 * Enum representing the approval status of media files in the application.
 *
 * @package App\Enums
 */
enum MediaStatus: string
{
    case Approved = 'approved';
    case NotApproved = 'not_approved';
}
