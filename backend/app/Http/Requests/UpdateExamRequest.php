<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                           => 'required|string|min:3|max:255',
            'description'                     => 'nullable|string|max:1000',
            'questions'                       => 'required|array|min:1',
            'questions.*.text'                => 'required|string|min:5',
            'questions.*.order'               => 'integer|min:0',
            'questions.*.options'             => 'required|array|min:2',
            'questions.*.options.*.text'      => 'required|string|min:1|max:500',
            'questions.*.options.*.is_correct' => 'boolean',
            'questions.*.options.*.order'     => 'integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'                  => 'O título da prova é obrigatório.',
            'questions.required'              => 'Adicione pelo menos uma questão.',
            'questions.*.text.required'       => 'O texto da questão é obrigatório.',
            'questions.*.options.required'    => 'Cada questão deve ter pelo menos 2 opções.',
            'questions.*.options.*.text.required' => 'Todas as opções devem ter texto.',
        ];
    }

    protected function passedValidation()
    {
        // Validar que cada questão tem exatamente 1 opção correta
        foreach ($this->input('questions', []) as $index => $question) {
            $correctCount = 0;
            foreach ($question['options'] ?? [] as $option) {
                if ($option['is_correct'] ?? false) {
                    $correctCount++;
                }
            }

            if ($correctCount === 0) {
                $this->validator->after(function ($validator) use ($index) {
                    $validator->errors()->add(
                        "questions.{$index}.options",
                        "A questão " . ($index + 1) . " deve ter exatamente uma opção correta."
                    );
                });
            } elseif ($correctCount > 1) {
                $this->validator->after(function ($validator) use ($index) {
                    $validator->errors()->add(
                        "questions.{$index}.options",
                        "A questão " . ($index + 1) . " pode ter apenas uma opção correta."
                    );
                });
            }
        }
    }
}
