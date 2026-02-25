<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sale_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'pix_price' => 'nullable|numeric|min:0',
            'stock_notice' => 'required|integer|min:0',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
            'bar_code' => 'nullable|string|max:255',
        ];
    }
}
