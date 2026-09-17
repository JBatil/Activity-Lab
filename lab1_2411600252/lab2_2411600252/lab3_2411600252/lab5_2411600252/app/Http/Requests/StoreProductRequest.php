<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Any authenticated user may create products in this lab system.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for creating a new product.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the product name.',
            'sku.required' => 'Please enter a SKU for this product.',
            'sku.unique' => 'This SKU is already in use by another product.',
            'category.required' => 'Please select or enter a category.',
            'quantity.min' => 'Quantity cannot be negative.',
            'reorder_level.min' => 'Reorder level cannot be negative.',
            'unit_price.min' => 'Unit price cannot be negative.',
        ];
    }
}
