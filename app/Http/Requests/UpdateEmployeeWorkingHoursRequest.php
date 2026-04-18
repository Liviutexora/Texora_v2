<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeWorkingHoursRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'days' => ['nullable', 'array'],
            'days.*' => ['nullable', 'array'],
            'days.*.*.start' => ['nullable', 'date_format:H:i'],
            'days.*.*.end' => ['nullable', 'date_format:H:i'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date_format' => 'Ora trebuie să fie în format HH:MM',
        ];
    }
}
