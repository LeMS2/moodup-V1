<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'date'  => ['required', 'date'],
        'level' => ['required', 'integer', 'min:1', 'max:5'],
        'note'  => ['nullable', 'string', 'max:2000'],

        'category_ids' => ['sometimes', 'array'],
        'category_ids.*' => ['integer', 'exists:categories,id'],
    ];
}
}
