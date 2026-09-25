<?php

namespace App\Http\Requests\Admin;

use App\Enums\FulfillmentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWalkInOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'fulfillment_method' => ['required', Rule::enum(FulfillmentMethod::class)],
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
