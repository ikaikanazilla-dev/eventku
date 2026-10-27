<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $event = $this->route('event');

        if (! $user) {
            return false;
        }

        return $event ? $user->can('update', $event) : $user->isRole('organizer');
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id,is_active,1'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'registration_start_at' => ['required', 'date', 'before:registration_end_at'],
            'registration_end_at' => ['required', 'date', 'before:start_at'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'ticket_name' => ['required', 'string', 'max:120'],
            'ticket_quota' => ['required', 'integer', 'min:1', 'max:100000'],
        ];
    }
}
