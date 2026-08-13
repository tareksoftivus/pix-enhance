<?php

namespace App\Http\Requests\Auth;

use App\Rules\TurnstileValid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // Phone is collected at sign-up only while SMS verification is on.
            'phone' => setting('require_sms_verification', false)
                ? 'required|string|max:20|unique:users,phone'
                : 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
            'cf-turnstile-response' => [new TurnstileValid],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
