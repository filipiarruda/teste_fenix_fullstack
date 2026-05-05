<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // Listar provas do professor autenticado
    public function index(Request $request): JsonResponse
    {
        $exams = Exam::where('user_id', $request->user()->id)
            ->with('questions.options')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($exams);
    }

    // Criar nova prova com questões e opções
    public function store(StoreExamRequest $request): JsonResponse
    {
        try {
            $exam = Exam::create([
                'user_id'     => $request->user()->id,
                'title'       => $request->input('title'),
                'description' => $request->input('description'),
            ]);

            $questions = $request->input('questions', []);
            foreach ($questions as $questionIndex => $questionData) {
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'text'    => $questionData['text'],
                    'order'   => $questionData['order'] ?? $questionIndex,
                ]);

                $options = $questionData['options'] ?? [];
                foreach ($options as $optionIndex => $optionData) {
                    Option::create([
                        'question_id' => $question->id,
                        'text'        => $optionData['text'],
                        'is_correct'  => $optionData['is_correct'] ?? false,
                        'order'       => $optionData['order'] ?? $optionIndex,
                    ]);
                }
            }

            $exam->load('questions.options');

            return response()->json(
                ['message' => 'Prova criada com sucesso.', 'exam' => $exam],
                201
            );
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Erro ao criar prova: ' . $e->getMessage()],
                500
            );
        }
    }

    // Obter uma prova específica
    public function show(Request $request, Exam $exam): JsonResponse
    {
        // Verificar autorização
        if ($exam->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $exam->load('questions.options');

        return response()->json($exam);
    }

    // Atualizar prova
    public function update(UpdateExamRequest $request, Exam $exam): JsonResponse
    {
        try {
            // Verificar autorização
            if ($exam->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Não autorizado.'], 403);
            }

            // Atualizar dados da prova
            $exam->update([
                'title'       => $request->input('title'),
                'description' => $request->input('description'),
            ]);

            // Deletar questões e opções existentes
            Question::where('exam_id', $exam->id)->delete();

            // Criar novas questões e opções
            $questions = $request->input('questions', []);
            foreach ($questions as $questionIndex => $questionData) {
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'text'    => $questionData['text'],
                    'order'   => $questionData['order'] ?? $questionIndex,
                ]);

                $options = $questionData['options'] ?? [];
                foreach ($options as $optionIndex => $optionData) {
                    Option::create([
                        'question_id' => $question->id,
                        'text'        => $optionData['text'],
                        'is_correct'  => $optionData['is_correct'] ?? false,
                        'order'       => $optionData['order'] ?? $optionIndex,
                    ]);
                }
            }

            $exam->load('questions.options');

            return response()->json(
                ['message' => 'Prova atualizada com sucesso.', 'exam' => $exam],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Erro ao atualizar prova: ' . $e->getMessage()],
                500
            );
        }
    }

    // Deletar prova
    public function destroy(Request $request, Exam $exam): JsonResponse
    {
        // Verificar autorização
        if ($exam->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        try {
            $exam->delete();

            return response()->json(['message' => 'Prova deletada com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(
                ['message' => 'Erro ao deletar prova: ' . $e->getMessage()],
                500
            );
        }
    }
}
