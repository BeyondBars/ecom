<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'type' => 'sometimes|string|in:info,success,warning,error',
            'icon' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'is_read' => 'sometimes|boolean',
            'read_at' => 'nullable|date',
        ];
    }
}
