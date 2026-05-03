<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use Illuminate\Support\Facades\Cache;

class ExamService
{
    public function getAllExams()
    {
        return Cache::remember("exams_list", 3600, function () {
            return Exam::with("professor", "questions")->get();
        });
    }

    public function getExamById($examId)
    {
        return Exam::with("professor", "questions")->find($examId);
    }

    public function createExam(array $data)
    {
        $exam = Exam::create($data);
        Cache::forget("exams_list");
        return $exam;
    }

    public function updateExam($examId, array $data)
    {
        $exam = Exam::find($examId);
        $exam->update($data);
        Cache::forget("exams_list");
        return $exam;
    }

    public function deleteExam($examId)
    {
        Exam::find($examId)->delete();
        Cache::forget("exams_list");
    }

    public function submitAnswers($examId, $studentId, $answers)
    {
        $exam = Exam::find($examId);
        $correctCount = 0;

        foreach ($answers as $questionId => $selectedAnswer) {
            ExamAnswer::updateOrCreate(
                ["exam_id" => $examId, "student_id" => $studentId, "question_id" => $questionId],
                ["selected_answer" => $selectedAnswer]
            );

            $question = $exam->questions()->find($questionId);
            if ($question && $question->correct_answer === $selectedAnswer) {
                $correctCount++;
            }
        }

        $score = ($correctCount / $exam->total_questions) * 100;
        $passed = $score >= $exam->passing_grade;

        $result = ExamResult::updateOrCreate(
            ["exam_id" => $examId, "student_id" => $studentId],
            [
                "correct_answers" => $correctCount,
                "total_questions" => $exam->total_questions,
                "score" => $score,
                "passed" => $passed
            ]
        );

        Cache::forget("exam_results_{$examId}_{$studentId}");
        return $result;
    }

    public function getResult($examId, $studentId)
    {
        return Cache::remember("exam_results_{$examId}_{$studentId}", 1800, function () use ($examId, $studentId) {
            return ExamResult::where("exam_id", $examId)->where("student_id", $studentId)->first();
        });
    }
}
