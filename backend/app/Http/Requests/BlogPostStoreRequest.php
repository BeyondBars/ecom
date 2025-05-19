<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create-blog-posts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'author_id' => ['nullable', 'exists:users,id'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft,archived'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
