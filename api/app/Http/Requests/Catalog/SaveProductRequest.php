<?php

namespace App\Http\Requests\Catalog;

use App\Models\Product;
use App\Support\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product ? $this->user()->can('update', $product) : $this->user()->can('create', Product::class);
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $required = $product ? 'sometimes' : 'required';

        return [
            'name' => [$required, 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($product)],
            'sku' => [$required, 'string', 'max:64', Rule::unique('products', 'sku')->ignore($product)],
            'category_id' => [$required, 'integer', Rule::exists('categories', 'id')],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')],
            'description' => ['nullable', 'string', 'max:20000'],
            'price' => [$required, 'numeric', 'min:0', 'max:9999999'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'stock' => [$required, 'integer', 'min:0', 'max:1000000'],
            'status' => ['sometimes', Rule::enum(ProductStatus::class)],
            'is_featured' => ['sometimes', 'boolean'],

            'options' => ['sometimes', 'nullable', 'array', 'max:5'],
            'options.*.name' => ['required', 'string', 'max:50', 'distinct'],
            'options.*.values' => ['required', 'array', 'min:1', 'max:30'],
            'options.*.values.*' => ['required', 'string', 'max:50', 'distinct'],

            'specifications' => ['sometimes', 'nullable', 'array', 'max:30'],
            'specifications.*.name' => ['required', 'string', 'max:100'],
            'specifications.*.value' => ['required', 'string', 'max:500'],

            'images' => ['sometimes', 'array', 'max:12'],
            'images.*.url' => ['required', 'string', 'max:2048'],
            'images.*.alt' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $price = $this->input('price', $this->route('product')?->price);
                $sale = $this->input('sale_price');

                if ($sale !== null && $price !== null && is_numeric($sale) && is_numeric($price) && $sale >= $price) {
                    $validator->errors()->add('sale_price', 'The sale price must be lower than the regular price.');
                }
            },
        ];
    }
}
