<?php namespace App\Http\Controllers;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use Illuminate\Http\Request;
class SubmissionController extends Controller {
    public function submit(Request $request) {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.selected_answer' => 'required|string|in:A,B,C,D'
        ]);

        $exam = Exam::with('questions')->find($validated['exam_id']);
        $studentId = auth()->id();

        if (ExamResult::where('exam_id', $exam->id)->where('student_id', $studentId)->exists()) {
            return response()->json(['error' => 'You have already submitted this exam'], 409);
        }

        $correctCount = 0;

        foreach ($validated['answers'] as $answerData) {
            $question = $exam->questions->firstWhere('id', $answerData['question_id']);
            if (!$question) continue;

            ExamAnswer::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $studentId, 'question_id' => $question->id],
                ['selected_answer' => $answerData['selected_answer']]
            );

            if ($question->correct_answer === $answerData['selected_answer']) {
                $correctCount++;
            }
        }

        $totalQuestions = $exam->total_questions;
        $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        $passed = $score >= $exam->passing_grade;

        $result = ExamResult::create([
            'exam_id' => $exam->id,
            'student_id' => $studentId,
            'correct_answers' => $correctCount,
            'total_questions' => $totalQuestions,
            'score' => $score,
            'passed' => $passed
        ]);

        return response()->json([
            'id' => $result->id,
            'exam_id' => $result->exam_id,
            'student_id' => $result->student_id,
            'correct_answers' => $result->correct_answers,
            'total_questions' => $result->total_questions,
            'score' => (float) $result->score,
            'percentage' => (float) $result->score,
            'passed' => $result->passed,
            'created_at' => $result->created_at,
            'updated_at' => $result->updated_at
        ], 201);
    }

    public function getResult(Exam $exam) {
        $result = $exam->results()->where('student_id', auth()->id())->first();
        if (!$result) {
            return response()->json(['message' => 'No result found'], 404);
        }
        return response()->json([
            'id' => $result->id,
            'exam_id' => $result->exam_id,
            'student_id' => $result->student_id,
            'correct_answers' => $result->correct_answers,
            'total_questions' => $result->total_questions,
            'score' => (float) $result->score,
            'percentage' => (float) $result->score,
            'passed' => $result->passed,
            'created_at' => $result->created_at,
            'updated_at' => $result->updated_at
        ]);
    }

    public function getUserAnswers(Exam $exam) {
        $answers = ExamAnswer::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->get(['question_id', 'selected_answer']);

        return response()->json($answers);
    }
}
