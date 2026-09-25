<?php

namespace App\Http\Requests\Customer;

use App\Enums\FulfillmentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fulfillment_method' => ['required', Rule::enum(FulfillmentMethod::class)],
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'scheduled_at' => ['nullable', 'date', 'after_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('idempotency_key') && $this->header('Idempotency-Key')) {
            $this->merge(['idempotency_key' => $this->header('Idempotency-Key')]);
        }
    }
}
