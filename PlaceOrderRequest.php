<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Guest checkout — no auth required for TechNova's express flow
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'string', 'regex:/^(?:\+?880|0)1[3-9]\d{8}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'district' => ['required', 'string', 'max:100'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'order_notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,bkash,nagad'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Please enter a valid Bangladeshi phone number (e.g. 01XXXXXXXXX).',
        ];
    }
}
