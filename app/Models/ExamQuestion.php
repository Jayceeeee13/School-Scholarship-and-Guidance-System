<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
     protected $fillable = [
        'exam_category_id',
        'exam_id',
        'question',
        'points',
        'order',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function choices()
    {
        return $this->hasMany(QuestionChoice::class)->orderBy('choice_letter');
    }

    public function examCategory()
    {
        return $this->belongsTo(ExamCategory::class);
    }

    public function correctChoice()
    {
        return $this->hasOne(QuestionChoice::class)->where('is_correct', true);
    }
}
