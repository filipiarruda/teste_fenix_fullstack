<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamResult extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'correct_answers',
        'total_questions',
    ];

    protected $casts = [
        'score' => 'integer',
        'correct_answers' => 'integer',
        'total_questions' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
