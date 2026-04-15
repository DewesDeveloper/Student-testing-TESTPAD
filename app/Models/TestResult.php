<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'test_id',
        'score',
        'total_points',
        'completed_at',
        'started_at',
        'answers',
        'student_comment',
        'is_reviewed',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
        'answers' => 'array',
        'is_reviewed' => 'boolean',
        'score' => 'float',
        'total_points' => 'float',
    ];


    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
