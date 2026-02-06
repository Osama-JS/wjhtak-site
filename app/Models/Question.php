<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question_ar', 'question_en', 'answer_ar', 'answer_en', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get localized question
     */
    public function getQuestionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->question_ar : $this->question_en;
    }

    /**
     * Get localized answer
     */
    public function getAnswerAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->answer_ar : $this->answer_en;
    }
}
