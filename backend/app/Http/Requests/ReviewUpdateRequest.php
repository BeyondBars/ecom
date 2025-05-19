<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Admin can update any review
        if ($this->user()->hasPermission('update_reviews')) {
            return true;
        }
        
        // Users can only update their own reviews
        return $this->review->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
        ];
        
        // Only admins can update these fields
        if ($this->user()->hasPermission('update_reviews')) {
            $rules['verified_purchase'] = ['sometimes', 'boolean'];
            $rules['approved'] = ['sometimes', 'boolean'];
            $rules['featured'] = ['sometimes', 'boolean'];
        }
        
        return $rules;
    }
}
