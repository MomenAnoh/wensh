<?php

namespace App\Http\Requests\Api\User\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/'],
            'code' => ['required', 'string', 'digits:6'],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => __('api.attributes.phone'),
            'code' => __('api.attributes.code'),
        ];
    }
}
