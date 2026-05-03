<?php namespace App\Http\Controllers;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
class AnalyticsController extends Controller {
    public function ranking(Exam $exam) {
        $ranking = $exam->results()
            ->with('student')
            ->orderByDesc('score')
            ->get()
            ->map(function($result, $position) {
                return [
                    'id' => $result->id,
                    'student_id' => $result->student_id,
                    'user' => $result->student ? [
                        'id' => $result->student->id,
                        'name' => $result->student->name,
                        'email' => $result->student->email
                    ] : null,
                    'score' => (float) $result->score,
                    'percentage' => (float) $result->score,
                    'correct_answers' => $result->correct_answers,
                    'total_questions' => $result->total_questions,
                    'passed' => $result->passed,
                    'position' => $position + 1
                ];
            });
        return response()->json($ranking);
    }

    public function average(Exam $exam) {
        $stats = $exam->results()->selectRaw('
            AVG(score) as avg_score,
            MIN(score) as min_score,
            MAX(score) as max_score,
            COUNT(*) as total_students,
            SUM(CASE WHEN passed THEN 1 ELSE 0 END) as passed_count
        ')->first();
        return response()->json(array_merge(
            $stats->toArray(),
            ['pass_rate' => $stats->total_students > 0 ? ($stats->passed_count / $stats->total_students * 100) : 0]
        ));
    }

    public function show(Exam $exam) {
        $stats = $exam->results()->selectRaw('
            AVG(score) as average_score,
            MIN(score) as min_score,
            MAX(score) as max_score,
            COUNT(*) as total_submissions,
            SUM(CASE WHEN passed THEN 1 ELSE 0 END) as passed_count
        ')->first();

        return response()->json([
            'exam_id' => $exam->id,
            'exam_title' => $exam->title,
            'average_score' => (float) ($stats->average_score ?? 0),
            'min_score' => (float) ($stats->min_score ?? 0),
            'max_score' => (float) ($stats->max_score ?? 0),
            'total_submissions' => (int) ($stats->total_submissions ?? 0),
            'passed_count' => (int) ($stats->passed_count ?? 0),
            'pass_rate' => $stats->total_students > 0 ? ($stats->passed_count / $stats->total_students * 100) : 0
        ]);
    }

    public function top(Exam $exam) {
        $top = $exam->results()
            ->with('student')
            ->orderByDesc('score')
            ->first();

        if (!$top) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $top->id,
            'student_id' => $top->student_id,
            'user' => $top->student ? [
                'id' => $top->student->id,
                'name' => $top->student->name,
                'email' => $top->student->email
            ] : null,
            'score' => (float) $top->score,
            'percentage' => (float) $top->score,
            'correct_answers' => $top->correct_answers,
            'total_questions' => $top->total_questions,
            'passed' => $top->passed
        ]);
    }
}
