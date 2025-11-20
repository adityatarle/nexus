<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization can be added based on requirements
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-Z\s\.]+$/', // Only letters, spaces, and dots
            ],
            'customer_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'customer_phone' => [
                'required',
                'regex:/^[6-9]\d{9}$/', // Indian mobile number format
                'size:10',
            ],
            'billing_address' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'shipping_address' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'city' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'state' => [
                'required',
                'string',
                'max:100',
            ],
            'pincode' => [
                'required',
                'regex:/^[1-9][0-9]{5}$/', // 6-digit Indian pincode
                'size:6',
            ],
            'payment_method' => [
                'required',
                'in:cod,online,bank_transfer',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'terms_accepted' => [
                'accepted',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Please enter your full name.',
            'customer_name.regex' => 'Name must contain only letters and spaces.',
            'customer_name.min' => 'Name must be at least 3 characters.',
            
            'customer_email.required' => 'Email address is required.',
            'customer_email.email' => 'Please enter a valid email address.',
            
            'customer_phone.required' => 'Phone number is required.',
            'customer_phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
            'customer_phone.size' => 'Phone number must be exactly 10 digits.',
            
            'billing_address.required' => 'Billing address is required.',
            'billing_address.min' => 'Billing address must be at least 10 characters.',
            
            'shipping_address.required' => 'Shipping address is required.',
            'shipping_address.min' => 'Shipping address must be at least 10 characters.',
            
            'city.required' => 'City is required.',
            'city.regex' => 'City name must contain only letters.',
            
            'state.required' => 'State is required.',
            
            'pincode.required' => 'Pincode is required.',
            'pincode.regex' => 'Please enter a valid 6-digit Indian pincode.',
            'pincode.size' => 'Pincode must be exactly 6 digits.',
            
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            
            'terms_accepted.accepted' => 'You must accept the terms and conditions to proceed.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'customer_name' => 'name',
            'customer_email' => 'email',
            'customer_phone' => 'phone number',
            'billing_address' => 'billing address',
            'shipping_address' => 'shipping address',
            'payment_method' => 'payment method',
            'terms_accepted' => 'terms and conditions',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs
        $this->merge([
            'customer_name' => strip_tags($this->customer_name ?? ''),
            'customer_email' => strtolower(trim($this->customer_email ?? '')),
            'customer_phone' => preg_replace('/[^0-9]/', '', $this->customer_phone ?? ''),
            'billing_address' => strip_tags($this->billing_address ?? ''),
            'shipping_address' => strip_tags($this->shipping_address ?? ''),
            'city' => strip_tags($this->city ?? ''),
            'state' => strip_tags($this->state ?? ''),
            'pincode' => preg_replace('/[^0-9]/', '', $this->pincode ?? ''),
            'notes' => strip_tags($this->notes ?? ''),
        ]);
    }
}

















