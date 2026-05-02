<?php

namespace App\Http\Requests\Admin;

use App\Support\Admin\AdminResourceRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource' => ['required', Rule::in(AdminResourceRegistry::keys())],
            'format' => ['required', Rule::in(['csv'])],
        ];
    }
}
