<?php

namespace App\Modules\Credits\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseCreditPackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'pack' => ['required', 'string', Rule::in(array_keys(config('credits.packs', [])))],
        ];
    }
}
