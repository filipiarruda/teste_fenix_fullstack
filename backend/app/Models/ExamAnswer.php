<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ExamAnswer extends Model {
    protected $table = 'exam_answers';
    protected $fillable = ['exam_id', 'student_id', 'question_id', 'selected_answer'];
    public function student(): BelongsTo { return $this->belongsTo(User::class); }
    public function exam(): BelongsTo { return $this->belongsTo(Exam::class); }
    public function question(): BelongsTo { return $this->belongsTo(Question::class); }
}
