<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update-blog-posts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'nullable', 
                'string', 
                'max:255', 
                Rule::unique('blog_posts')->ignore($this->blog_post)
            ],
            'content' => ['sometimes', 'string'],
            'excerpt' => ['nullable', 'string'],
            'author_id' => ['nullable', 'exists:users,id'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:published,draft,archived'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
