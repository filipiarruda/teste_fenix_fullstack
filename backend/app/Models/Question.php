<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Question extends Model {
    protected $fillable = ['exam_id', 'content', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'order'];
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function answers(): HasMany { return $this->hasMany(ExamAnswer::class); }
}
