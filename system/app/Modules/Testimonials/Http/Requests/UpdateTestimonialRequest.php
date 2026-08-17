<?php

namespace App\Modules\Testimonials\Http\Requests;

class UpdateTestimonialRequest extends TestimonialRequest
{
    public function rules(): array
    {
        return $this->sharedRules();
    }
}
