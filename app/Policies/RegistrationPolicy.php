<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    public function view(User $user, Registration $registration): bool
    {
        return $user->isRole('admin') || $registration->user_id === $user->id || ($user->isRole('organizer') && $registration->event?->organizer_id === $user->id);
    }
}
