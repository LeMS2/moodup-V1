<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMoodRequest extends FormRequest
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
        'title' => ['nullable', 'string', 'max:255'],
        'date' => ['nullable', 'date'],
        'level' => ['nullable', 'integer', 'min:1', 'max:5'],
        'score' => ['nullable', 'integer', 'min:0', 'max:10'],
        'note' => ['nullable', 'string'],
        'mood' => ['nullable', 'string', 'max:50'],
        'triggers' => ['nullable', 'array'],
        'triggers.*' => ['string', 'max:60'],

        'category_ids' => ['nullable', 'array'],
        'category_ids.*' => ['integer'],
    ];
}
}
