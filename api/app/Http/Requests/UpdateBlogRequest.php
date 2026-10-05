<?php

namespace App\Http\Requests;

use App\Support\Enums\BlogStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('blog'));
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blogs', 'slug')->ignore($this->route('blog'))],
            'content' => ['sometimes', 'required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', Rule::enum(BlogStatus::class)],
        ];
    }
}
