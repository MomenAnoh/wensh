<?php

namespace App\Http\Requests\Api\User\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('api') !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'car_type_id' => ['required', 'integer', 'exists:car_types,id'],
            'from_latitude' => ['required', 'numeric', 'between:-90,90'],
            'from_longitude' => ['required', 'numeric', 'between:-180,180'],
            'to_latitude' => ['required', 'numeric', 'between:-90,90'],
            'to_longitude' => ['required', 'numeric', 'between:-180,180'],
            'type' => ['required', 'string', Rule::in(['normal', 'scheduled'])],
            'scheduled_day' => ['required_if:type,scheduled', 'nullable', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required_if:type,scheduled', 'nullable', 'date_format:H:i'],
            'payment_methods' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
