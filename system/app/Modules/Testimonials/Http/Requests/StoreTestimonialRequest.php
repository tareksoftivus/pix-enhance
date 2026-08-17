<?php

namespace App\Modules\Testimonials\Http\Requests;

class StoreTestimonialRequest extends TestimonialRequest
{
    public function rules(): array
    {
        return $this->sharedRules();
    }
}
