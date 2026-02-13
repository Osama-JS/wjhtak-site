<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question_ar', 'question_en', 'answer_ar', 'answer_en'];

    /**
     * Get question
     */
    public function getQuestionAttribute($value)
    {
        return $value;
    }

    /**
     * Get answer
     */
    public function getAnswerAttribute($value)
    {
        return $value;
    }
}
