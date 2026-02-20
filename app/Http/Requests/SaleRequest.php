<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.produtoId' => 'required|integer|exists:products,id',
            'items.*.quantidade' => 'required|integer|min:1',
            'items.*.precoUnitario' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'pagamento' => 'required|array',
            'pagamento.forma' => 'required|string|in:cash,card,PIX',
            'pagamento.valorRecebido' => 'required|numeric',
            'pagamento.troco' => 'required|numeric',
            'id_cashier' => 'required|integer|exists:cashiers,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Pelo menos um item é obrigatório na venda.',
            'items.array' => 'Os itens devem ser um array.',
            'items.min' => 'Pelo menos um item é obrigatório na venda.',
            'items.*.produtoId.required' => 'O ID do produto é obrigatório.',
            'items.*.produtoId.exists' => 'O produto selecionado não existe.',
            'items.*.quantidade.required' => 'A quantidade é obrigatória.',
            'items.*.quantidade.min' => 'A quantidade deve ser pelo menos 1.',
            'items.*.precoUnitario.required' => 'O preço unitário é obrigatório.',
            'items.*.precoUnitario.min' => 'O preço unitário deve ser maior ou igual a zero.',
            'items.*.subtotal.required' => 'O subtotal do item é obrigatório.',
            'subtotal.required' => 'O subtotal é obrigatório.',
            'total.required' => 'O total é obrigatório.',
            'pagamento.required' => 'As informações de pagamento são obrigatórias.',
            'pagamento.forma.required' => 'A forma de pagamento é obrigatória.',
            'pagamento.forma.in' => 'Forma de pagamento deve ser: dinheiro, cartão ou PIX.',
            'pagamento.valorRecebido.required' => 'O valor recebido é obrigatório.',
            'pagamento.troco.required' => 'O valor do troco é obrigatório.',
            'id_cashier.exists' => 'O caixa selecionado não existe.',
        ];
    }
}
