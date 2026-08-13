<?php

namespace App\Modules\Support\Http\Requests;

use App\Modules\Support\Models\SupportTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', Rule::in(array_keys(SupportTicket::priorities()))],
        ];
    }
}
