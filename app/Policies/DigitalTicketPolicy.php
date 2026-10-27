<?php

namespace App\Policies;

use App\Models\DigitalTicket;
use App\Models\User;

class DigitalTicketPolicy
{
    public function view(User $user, DigitalTicket $ticket): bool
    {
        $ticket->loadMissing('registration.event');

        return $user->isRole('admin') || $ticket->registration->user_id === $user->id || ($user->isRole('organizer') && $ticket->registration->event->organizer_id === $user->id);
    }
}
