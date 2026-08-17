<?php

namespace App\Modules\Testimonials\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function sharedRules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string', 'max:5000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
            'avatar' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Please enter the client name.',
            'quote.required' => 'Please enter the testimonial quote.',
            'rating.integer' => 'The rating must be a whole number.',
            'rating.min' => 'The rating must be at least 1.',
            'rating.max' => 'The rating may not be greater than 5.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'rating' => $this->filled('rating') ? (int) $this->input('rating') : null,
            'sort_order' => $this->filled('sort_order') ? (int) $this->input('sort_order') : 0,
            'active' => $this->has('active') ? $this->boolean('active') : true,
        ]);
    }
}
