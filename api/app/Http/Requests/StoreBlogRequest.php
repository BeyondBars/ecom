<?php

namespace App\Http\Requests;

use App\Models\Blog;
use App\Support\Enums\BlogStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Blog::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            // Optional: generated from the title when omitted.
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blogs', 'slug')],
            'content' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(BlogStatus::class)],
        ];
    }
}
