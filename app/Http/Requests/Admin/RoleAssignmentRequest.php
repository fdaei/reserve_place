<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists('users', 'id')],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'کاربر پنل',
            'roles' => 'نقش‌ها',
        ];
    }
}
