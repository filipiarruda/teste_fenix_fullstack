<?php namespace App\Http\Controllers;
use App\Models\Exam;
use Illuminate\Http\Request;
class ExamController extends Controller {
    public function index() {
        $user = auth()->user();
        if ($user->role === 'professor') {
            $exams = Exam::where('professor_id', $user->id)->with('questions', 'results')->get();
        } else {
            $exams = Exam::with('professor', 'questions')->get();
        }
        return response()->json($exams);
    }

    public function store(Request $request) {
        $user = auth()->user();
        if ($user->role !== 'professor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'passing_grade' => 'sometimes|integer|min:0|max:100'
        ]);

        $validated['professor_id'] = $user->id;
        $exam = Exam::create($validated);
        return response()->json($exam, 201);
    }

    public function show(Exam $exam) {
        return response()->json($exam->load('professor', 'questions'));
    }

    public function update(Request $request, Exam $exam) {
        if (auth()->id() !== $exam->professor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $exam->update($request->validate([
            'title' => 'string',
            'description' => 'string',
            'passing_grade' => 'integer|min:0|max:100'
        ]));
        return response()->json($exam);
    }

    public function destroy(Exam $exam) {
        if (auth()->id() !== $exam->professor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $exam->delete();
        return response()->json(null, 204);
    }
}
