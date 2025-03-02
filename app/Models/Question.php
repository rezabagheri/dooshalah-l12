<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'order_in_page',
        'question',
        'answer_type',
        'category',
        'search_label',
        'answer_label',
        'is_required',
        'is_editable',
        'is_visible',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_editable' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'question_id');
    }
}
