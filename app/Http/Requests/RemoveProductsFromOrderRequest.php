<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveProductsFromOrderRequest extends FormRequest
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
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|integer|exists:products,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'product_ids.required' => 'É necessário informar pelo menos um produto para remover.',
            'product_ids.min' => 'É necessário informar pelo menos um produto para remover.',
            'product_ids.*.required' => 'O ID do produto é obrigatório.',
            'product_ids.*.exists' => 'O produto selecionado não existe.',
        ];
    }
}
