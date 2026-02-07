<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'answer'];

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
