<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexStockRequest extends FormRequest
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
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'product_ids.array' => 'O campo product_ids deve ser um array.',
            'product_ids.*.integer' => 'Cada product_id deve ser um número inteiro.',
            'product_ids.*.exists' => 'Um dos produtos informados não existe.',
        ];
    }
}
