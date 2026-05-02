<?php

namespace App\Http\Requests\Admin;

use App\Support\Admin\AdminResourceRegistry;
use Illuminate\Foundation\Http\FormRequest;

class AdminResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $resource = (string) $this->route('resource');

        if (AdminResourceRegistry::exists($resource)) {
            $this->merge(AdminResourceRegistry::prepareForValidation($resource, $this->all()));
        }
    }

    public function rules(): array
    {
        $resource = (string) $this->route('resource');
        $config = AdminResourceRegistry::get($resource);
        $modelClass = $config['model'];
        $model = $this->route('id') ? $modelClass::find($this->route('id')) : null;

        return AdminResourceRegistry::rules($resource, $model);
    }

    public function attributes(): array
    {
        $resource = (string) $this->route('resource');

        return AdminResourceRegistry::attributes($resource);
    }

    public function messages(): array
    {
        $messages = [
            'required' => 'وارد کردن :attribute الزامی است.',
            'required_if' => 'وارد کردن :attribute الزامی است.',
            'integer' => ':attribute باید عدد صحیح باشد.',
            'numeric' => ':attribute باید عدد باشد.',
            'digits' => ':attribute باید دقیقاً :digits رقم باشد.',
            'digits_between' => ':attribute باید بین :min تا :max رقم باشد.',
            'date' => ':attribute باید تاریخ معتبر باشد.',
            'after' => ':attribute باید بعد از تاریخ شروع باشد.',
            'min' => ':attribute کمتر از مقدار مجاز است.',
            'max' => ':attribute بیشتر از مقدار مجاز است.',
            'string' => ':attribute باید متن معتبر باشد.',
            'exists' => ':attribute انتخاب شده معتبر نیست.',
            'unique' => ':attribute قبلاً ثبت شده است.',
            'boolean' => ':attribute باید بله یا خیر باشد.',
            'image' => ':attribute باید تصویر معتبر باشد.',
            'file' => ':attribute باید فایل معتبر باشد.',
            'mimes' => 'فرمت :attribute معتبر نیست.',
            'national_code.digits' => 'کد ملی باید دقیقاً ۱۰ رقم باشد.',
            'ends_at.after' => 'تاریخ پایان باید بعد از تاریخ شروع باشد.',
            'expires_at.after' => 'تاریخ انقضا باید بعد از تاریخ شروع باشد.',
        ];

        if ((string) $this->route('resource') === 'settlements') {
            return $messages + [
                'amount.required' => 'وارد کردن مبلغ تسویه الزامی است.',
                'amount.integer' => 'مبلغ تسویه باید عدد صحیح باشد.',
                'amount.min' => 'مبلغ تسویه باید بیشتر از صفر باشد.',
            ];
        }

        return $messages;
    }
}
