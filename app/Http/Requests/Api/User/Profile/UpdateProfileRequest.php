<?php

namespace App\Http\Requests\Api\User\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user('api')->getKey()),
            ],
            'emergency_contact_phone' => ['nullable', 'string', 'regex:/^\+[1-9]\d{7,14}$/'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('api.attributes.name'),
            'email' => __('api.attributes.email'),
            'emergency_contact_phone' => __('api.attributes.emergency_contact_phone'),
            'profile_photo' => __('api.attributes.profile_photo'),
        ];
    }
}
