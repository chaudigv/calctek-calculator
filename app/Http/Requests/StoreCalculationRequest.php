<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expression' => ['required', 'string', 'max:500', 'regex:/^[\d\s\+\-\*\/\^\%\.\(\)a-zA-Z]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'expression.regex' => 'Expression contains invalid characters',
        ];
    }
}
