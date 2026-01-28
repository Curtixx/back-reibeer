<?php

namespace App\Http\Requests;

use App\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexOrderRequest extends FormRequest
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
            'number' => 'nullable|string',
            'responsible_name' => 'nullable|string',
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
            'product_id' => 'nullable|integer|exists:products,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'number.string' => 'O número da comanda deve ser um texto.',
            'responsible_name.string' => 'O nome do responsável deve ser um texto.',
            'status' => 'O status deve ser "open" ou "closed".',
            'product_id.integer' => 'O ID do produto deve ser um número inteiro.',
            'product_id.exists' => 'O produto selecionado não existe.',
        ];
    }
}
