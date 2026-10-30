<?php

namespace App\Http\Requests\Api\User\Notification;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enabled' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'enabled' => __('api.attributes.enabled'),
        ];
    }
}
