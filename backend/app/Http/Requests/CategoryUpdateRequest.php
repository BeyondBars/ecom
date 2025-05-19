<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update-categories');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'nullable', 
                'string', 
                'max:255', 
                Rule::unique('categories')->ignore($this->category)
            ],
            'description' => ['nullable', 'string'],
            'parent_id' => [
                'nullable', 
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value == $this->category->id) {
                        $fail('A category cannot be its own parent.');
                    }
                },
            ],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
