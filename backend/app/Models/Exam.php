<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Exam extends Model {
    protected $fillable = ['professor_id', 'title', 'description', 'total_questions', 'passing_grade'];
    public function professor(): BelongsTo { return $this->belongsTo(User::class, 'professor_id'); }
    public function questions(): HasMany { return $this->hasMany(Question::class); }
    public function answers(): HasMany { return $this->hasMany(ExamAnswer::class); }
    public function results(): HasMany { return $this->hasMany(ExamResult::class); }
}
