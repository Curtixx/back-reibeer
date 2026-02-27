<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
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
            'ids.*' => 'integer|exists:categories,id',
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
            'ids.*.exists' => 'Uma das categorias informadas não existe.',
        ];
    }
}
