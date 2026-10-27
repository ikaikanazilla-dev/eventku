<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'participant';
    }

    public function rules(): array
    {
        return ['ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id']];
    }
}
