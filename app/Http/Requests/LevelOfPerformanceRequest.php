<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class LevelOfPerformanceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'performance_name' => [
                'required',
                Rule::unique('level_of_performances')->where('status', 1),
            ]
        ];
    }

    public function messages()
    {
        return [
            'performance_name.required' => 'The name field is required.',
        ];
    }
}
