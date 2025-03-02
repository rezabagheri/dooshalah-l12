<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMatchScore extends Model
{
    protected $fillable = ['user_id', 'target_id', 'match_score'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
