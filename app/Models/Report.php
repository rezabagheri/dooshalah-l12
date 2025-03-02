<?php

namespace App\Models;

use App\Enums\FriendshipStatus;
use App\Enums\Severity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_id',
        'report',
        'answer',
        'page_url',
        'user_agent',
        'review_at',
        'status',
        'severity',
    ];

    protected $casts = [
        'review_at' => 'datetime',
        'status' => FriendshipStatus::class,
        'severity' => Severity::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
