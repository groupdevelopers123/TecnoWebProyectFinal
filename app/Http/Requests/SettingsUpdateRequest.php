<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            'theme' => ['nullable', Rule::in(['ninos', 'adultos', 'jovenes'])],
            'mode' => ['nullable', Rule::in(['dark', 'light'])],
            'font_size' => ['nullable', 'integer', 'between:12,36'],
            'contrast' => ['nullable', Rule::in(['normal', 'high'])],
            'current_password' => ['nullable', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
