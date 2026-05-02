<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'min:10', 'max:11'],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => 'شماره موبایل',
        ];
    }
}
