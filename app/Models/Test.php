<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'discipline_id',
        'is_active',
        'type',
        'tags',
        'image',
        'instruction',
        'author',
        'source',
        'enable_grading',
        'grading_type',
        'grade_5_threshold',
        'grade_4_threshold',
        'grade_3_threshold',
        'grade_label',
        'show_result_to_user',
        'show_numbers',
        'allow_comments',
        'allow_error_reports',
        'shuffle_questions',
        'shuffle_options',
        'require_all_answers',
        'show_progress_bar',
        'show_time',
        'limit_time',
        'time_limit',
        'prevent_copy',
        'prevent_back',
        'confirm_next',
        'confirm_finish',
        'show_correct_instantly',
        'show_dropdown',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function results()
    {
        return $this->hasMany(TestResult::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }
}
