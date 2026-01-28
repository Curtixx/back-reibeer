<?php

namespace App\Http\Requests;

use App\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'number' => 'required|string|unique:orders,number',
            'responsible_name' => 'required|string',
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'number.required' => 'O número da comanda é obrigatório.',
            'number.unique' => 'Este número de comanda já está em uso.',
            'responsible_name.required' => 'O nome do responsável é obrigatório.',
            'status' => 'O status deve ser "open" ou "closed".',
            'products.required' => 'A comanda deve ter pelo menos um produto.',
            'products.min' => 'A comanda deve ter pelo menos um produto.',
            'products.*.id.required' => 'O ID do produto é obrigatório.',
            'products.*.id.exists' => 'O produto selecionado não existe.',
            'products.*.quantity.required' => 'A quantidade do produto é obrigatória.',
            'products.*.quantity.min' => 'A quantidade do produto deve ser no mínimo 1.',
        ];
    }
}
