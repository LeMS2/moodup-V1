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
            'title' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'level' => ['required', 'integer', 'min:1', 'max:5'],
            'score' => ['nullable', 'integer', 'min:0', 'max:10'],
            'note' => ['nullable', 'string'],
            'mood' => ['nullable', 'string', 'max:50'],
            'trigger_ids' => ['nullable', 'array'],        // ← MUDAR DE 'triggers' para 'trigger_ids'
            'trigger_ids.*' => ['exists:triggers,id'],    // ← Validar se o ID existe no banco
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }

    // Opcional: Mensagens personalizadas
    public function messages(): array
    {
        return [
            'trigger_ids.*.exists' => 'Um ou mais gatilhos selecionados são inválidos.',
            'date.required' => 'A data é obrigatória.',
            'level.required' => 'O nível de humor é obrigatório.',
        ];
    }
}