<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentExamController extends Controller
{
    /**
     * Listar provas disponíveis para o aluno responder
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Buscar provas que o aluno ainda não respondeu
        $exams = Exam::where('published', true)
            ->with('questions', 'questions.options')
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('exam_id')
                    ->from('exam_results')
                    ->where('user_id', $user->id);
            })
            ->get();

        return response()->json($exams);
    }

    /**
     * Mostrar uma prova específica para responder
     */
    public function show(Request $request, Exam $exam)
    {
        $user = $request->user();

        // Verificar se o aluno já respondeu essa prova
        $result = ExamResult::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($result) {
            return response()->json([
                'message' => 'Você já respondeu essa prova',
                'result' => $result->load('answers')
            ], 403);
        }

        // Retornar a prova com questões e opções
        $exam->load('questions', 'questions.options');

        return response()->json($exam);
    }

    /**
     * Submeter respostas e calcular resultado
     */
    public function submit(Request $request, Exam $exam)
    {
        $user = $request->user();

        // Validar se já respondeu
        $existingResult = ExamResult::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingResult) {
            return response()->json([
                'message' => 'Você já respondeu essa prova'
            ], 403);
        }

        // Validar dados
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.selected_option_id' => 'nullable|integer|exists:options,id',
        ]);

        try {
            DB::beginTransaction();

            // Criar resultado da prova
            $result = ExamResult::create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'score' => 0,
                'correct_answers' => 0,
                'total_questions' => count($validated['answers']),
            ]);

            $correctCount = 0;

            // Processar cada resposta
            foreach ($validated['answers'] as $answer) {
                $question = $exam->questions()->find($answer['question_id']);

                if (!$question) {
                    continue;
                }

                $isCorrect = false;

                // Se uma opção foi selecionada
                if ($answer['selected_option_id']) {
                    $selectedOption = $question->options()->find($answer['selected_option_id']);

                    if ($selectedOption && $selectedOption->is_correct) {
                        $isCorrect = true;
                        $correctCount++;
                    }
                }

                // Salvar a resposta
                ExamAnswer::create([
                    'exam_result_id' => $result->id,
                    'question_id' => $answer['question_id'],
                    'selected_option_id' => $answer['selected_option_id'] ?? null,
                    'is_correct' => $isCorrect,
                ]);
            }

            // Atualizar score
            $score = $result->total_questions > 0
                ? intval(($correctCount / $result->total_questions) * 100)
                : 0;

            $result->update([
                'score' => $score,
                'correct_answers' => $correctCount,
            ]);

            DB::commit();

            // Retornar resultado com as respostas
            $result->load('answers.selectedOption', 'answers.question');

            return response()->json([
                'message' => 'Prova respondida com sucesso!',
                'result' => $result
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao salvar respostas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver resultado de uma prova já respondida
     */
    public function result(Request $request, Exam $exam)
    {
        $user = $request->user();

        $result = ExamResult::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->with('answers.question.options', 'answers.selectedOption')
            ->first();

        if (!$result) {
            return response()->json([
                'message' => 'Você ainda não respondeu essa prova'
            ], 404);
        }

        return response()->json($result);
    }
}
