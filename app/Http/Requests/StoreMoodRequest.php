<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 📌 CAMPOS DO MOOD (OBRIGATÓRIOS)
            'title' => ['required', 'string', 'max:255'],  // ← REQUIRED
            'date' => ['required', 'date'],
            'level' => ['required', 'integer', 'min:1', 'max:5'],
            'score' => ['nullable', 'integer', 'min:0', 'max:10'],
            'note' => ['nullable', 'string'],
            'mood' => ['required', 'string', 'max:50'],  // ← REQUIRED
            
            // 📌 TRIGGERS
            'trigger_ids' => ['nullable', 'array'],
            'trigger_ids.*' => ['exists:triggers,id'],
            
            // 📌 CATEGORIAS
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],

            'trigger_ids' => 'nullable|array',
            'trigger_ids.*' => 'exists:triggers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'date.required' => 'A data é obrigatória.',
            'level.required' => 'O nível de humor é obrigatório.',
            'mood.required' => 'O humor é obrigatório.',
        ];
    }
}