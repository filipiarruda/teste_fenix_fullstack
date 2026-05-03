<?php namespace App\Http\Controllers;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
class QuestionController extends Controller {
    public function index(Exam $exam) {
        return response()->json($exam->questions()->orderBy('order')->get());
    }

    public function store(Request $request, Exam $exam) {
        $validated = $request->validate([
            'text' => 'required|string',
            'content' => 'sometimes|string',
            'order' => 'required|integer',
            'alternatives' => 'sometimes|array',
            'option_a' => 'sometimes|string',
            'option_b' => 'sometimes|string',
            'option_c' => 'sometimes|string',
            'option_d' => 'sometimes|string',
            'correct_answer' => 'sometimes|in:A,B,C,D'
        ]);

        if (!empty($validated['alternatives'])) {
            $correctIndex = null;
            foreach ($validated['alternatives'] as $idx => $alt) {
                if (!empty($alt['is_correct'])) {
                    $correctIndex = $idx;
                    break;
                }
            }
            if ($correctIndex === null) {
                return response()->json(['error' => 'At least one alternative must be correct'], 422);
            }

            $letters = ['A', 'B', 'C', 'D'];
            $data = [
                'exam_id' => $exam->id,
                'content' => $validated['text'] ?? $validated['content'] ?? '',
                'order' => $validated['order'],
                'correct_answer' => $letters[$correctIndex] ?? 'A'
            ];

            foreach ($validated['alternatives'] as $idx => $alt) {
                $letter = strtolower($letters[$idx] ?? $idx);
                $data["option_{$letter}"] = $alt['text'] ?? '';
            }

            $question = Question::create($data);
        } else {
            $data = [
                'exam_id' => $exam->id,
                'content' => $validated['content'] ?? $validated['text'] ?? '',
                'option_a' => $validated['option_a'],
                'option_b' => $validated['option_b'],
                'option_c' => $validated['option_c'],
                'option_d' => $validated['option_d'],
                'correct_answer' => $validated['correct_answer'],
                'order' => $validated['order']
            ];
            $question = Question::create($data);
        }

        return response()->json($question, 201);
    }

    public function update(Request $request, Question $question) {
        $validated = $request->validate([
            'text' => 'sometimes|string',
            'content' => 'sometimes|string',
            'order' => 'sometimes|integer',
            'alternatives' => 'sometimes|array',
            'option_a' => 'sometimes|string',
            'option_b' => 'sometimes|string',
            'option_c' => 'sometimes|string',
            'option_d' => 'sometimes|string',
            'correct_answer' => 'sometimes|in:A,B,C,D'
        ]);

        if (!empty($validated['alternatives'])) {
            $correctIndex = null;
            foreach ($validated['alternatives'] as $idx => $alt) {
                if (!empty($alt['is_correct'])) {
                    $correctIndex = $idx;
                    break;
                }
            }

            $letters = ['A', 'B', 'C', 'D'];
            $data = [];

            if (isset($validated['text'])) {
                $data['content'] = $validated['text'];
            }
            if (isset($validated['order'])) {
                $data['order'] = $validated['order'];
            }
            if ($correctIndex !== null) {
                $data['correct_answer'] = $letters[$correctIndex] ?? 'A';
            }

            foreach ($validated['alternatives'] as $idx => $alt) {
                $letter = strtolower($letters[$idx] ?? $idx);
                $data["option_{$letter}"] = $alt['text'] ?? $question->{"option_{$letter}"};
            }

            $question->update($data);
        } else {
            $data = [];
            if (isset($validated['content'])) $data['content'] = $validated['content'];
            if (isset($validated['text'])) $data['content'] = $validated['text'];
            if (isset($validated['option_a'])) $data['option_a'] = $validated['option_a'];
            if (isset($validated['option_b'])) $data['option_b'] = $validated['option_b'];
            if (isset($validated['option_c'])) $data['option_c'] = $validated['option_c'];
            if (isset($validated['option_d'])) $data['option_d'] = $validated['option_d'];
            if (isset($validated['correct_answer'])) $data['correct_answer'] = $validated['correct_answer'];
            if (isset($validated['order'])) $data['order'] = $validated['order'];

            if (!empty($data)) {
                $question->update($data);
            }
        }

        return response()->json($question);
    }

    public function destroy(Question $question) {
        $question->delete();
        return response()->json(null, 204);
    }
}
