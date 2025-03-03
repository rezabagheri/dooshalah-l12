<?php

namespace App\Enums;

enum NotificationType: string
{
    case FriendRequest = 'friend_request';
    case FriendAccepted = 'friend_accepted';
    case PaymentSuccess = 'payment_success';
    case PaymentFailed = 'payment_failed';
    case NewMessage = 'new_message';
    case NewChatMessage = 'new_chat_message';
    case AdminMessage = 'admin_message';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FriendRequest => 'Friend Request',
            self::FriendAccepted => 'Friend Accepted',
            self::PaymentSuccess => 'Payment Successful',
            self::PaymentFailed => 'Payment Failed',
            self::NewMessage => 'New Message',
            self::NewChatMessage => 'New Chat Message',
            self::AdminMessage => 'Admin Message',
            self::Other => 'Other',
        };
    }
}
