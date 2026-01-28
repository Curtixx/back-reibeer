<?php

namespace App\Http\Requests;

use App\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'number' => 'sometimes|string|unique:orders,number,'.$this->route('order')->id,
            'responsible_name' => 'sometimes|string',
            'status' => ['sometimes', Rule::enum(OrderStatus::class)],
            'products' => 'sometimes|array|min:1',
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
            'number.unique' => 'Este número de comanda já está em uso.',
            'status' => 'O status deve ser "open" ou "closed".',
            'products.min' => 'A comanda deve ter pelo menos um produto.',
            'products.*.id.required' => 'O ID do produto é obrigatório.',
            'products.*.id.exists' => 'O produto selecionado não existe.',
            'products.*.quantity.required' => 'A quantidade do produto é obrigatória.',
            'products.*.quantity.min' => 'A quantidade do produto deve ser no mínimo 1.',
        ];
    }
}
