<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'min:7', 'max:30', 'regex:/^\+?[0-9\s()\-]+$/'],
        ];
    }

    public function messages(): array
    {
        return ['contact_number.regex' => 'Use digits, spaces, parentheses or hyphens, with an optional leading +.'];
    }
}
