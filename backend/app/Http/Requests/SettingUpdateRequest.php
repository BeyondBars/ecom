<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update-settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Dynamic rules based on the settings being updated
        $rules = [];
        
        foreach ($this->keys() as $key) {
            if ($key !== 'group') {
                $rules[$key] = ['required'];
            }
        }
        
        return $rules;
    }
}
