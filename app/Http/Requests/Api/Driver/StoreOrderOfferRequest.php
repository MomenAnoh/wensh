<?php

namespace App\Http\Requests\Api\Driver;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('wensh') !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'price' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
