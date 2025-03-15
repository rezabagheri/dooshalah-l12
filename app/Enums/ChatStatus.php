<?php

namespace App\Enums;

/**
 * Enum representing the status of a chat message in the application.
 *
 * @package App\Enums
 */
enum ChatStatus: string
{
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Read = 'read';
}
