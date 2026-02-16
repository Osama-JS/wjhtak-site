<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question_ar', 'question_en', 'answer_ar', 'answer_en'];

    /**
     * Get question based on locale
     */
    public function getQuestionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->question_ar : $this->question_en;
    }

    /**
     * Get answer based on locale
     */
    public function getAnswerAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->answer_ar : $this->answer_en;
    }
}
