<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'answer',
    ];

    protected $casts = [
        'answer' => 'array', // JSON به‌صورت آرایه کست می‌شه
    ];

    // رابط با User (هر جواب متعلق به یک کاربر است)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // رابط با Question (هر جواب متعلق به یک پرسش است)
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
