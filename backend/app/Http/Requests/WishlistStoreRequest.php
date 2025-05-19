<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishlistStoreRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If user_id is not provided, use the authenticated user's ID
        if (!$this->has('user_id') && auth()->check()) {
            $this->merge([
                'user_id' => auth()->id(),
            ]);
        }
    }
}
