<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow guests to create comments, but admin approval may be required
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'author_id' => ['nullable', 'exists:users,id'],
            'author_name' => ['required_without:author_id', 'string', 'max:255'],
            'author_email' => ['required_without:author_id', 'string', 'email', 'max:255'],
            'status' => ['nullable', 'in:approved,pending,rejected'],
            'commentable_id' => ['required', 'integer'],
            'commentable_type' => ['required', 'string', 'in:App\\Models\\Product,App\\Models\\BlogPost'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set default status to pending if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'pending']);
        }
    }
}
