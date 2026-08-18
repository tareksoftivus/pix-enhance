<?php

namespace App\Modules\Credits\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('credits.adjust') ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'integer', 'not_in:0', 'min:-1000000', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
