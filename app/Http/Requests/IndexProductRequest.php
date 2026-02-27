<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
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
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:products,id',
            'bar_code' => 'nullable|string',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'ids.array' => 'O campo ids deve ser um array.',
            'ids.*.integer' => 'Cada id deve ser um número inteiro.',
            'ids.*.exists' => 'Um dos produtos informados não existe.',
            'bar_code.string' => 'O código de barras deve ser um texto.',
        ];
    }
}
