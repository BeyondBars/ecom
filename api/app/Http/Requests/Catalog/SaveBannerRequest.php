<?php

namespace App\Http\Requests\Catalog;

use App\Models\Banner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $banner = $this->route('banner');

        return $banner ? $this->user()->can('update', $banner) : $this->user()->can('create', Banner::class);
    }

    public function rules(): array
    {
        $required = $this->route('banner') ? 'sometimes' : 'required';

        return [
            'title' => [$required, 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'image_url' => [$required, 'string', 'max:2048'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'placement' => ['sometimes', Rule::in(Banner::PLACEMENTS)],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', ...($this->filled('starts_at') ? ['after:starts_at'] : [])],
        ];
    }
}
