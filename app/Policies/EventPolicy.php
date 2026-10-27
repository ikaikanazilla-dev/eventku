<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function view(User $user, Event $event): bool
    {
        return $user->isRole('admin') || $event->organizer_id === $user->id;
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isRole('admin') || ($user->isRole('organizer') && $event->organizer_id === $user->id && ! in_array($event->status, ['published', 'completed'], true));
    }

    public function submit(User $user, Event $event): bool
    {
        return $user->isRole('organizer') && $event->organizer_id === $user->id && in_array($event->status, ['draft', 'rejected'], true);
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isRole('organizer')
            && $event->organizer_id === $user->id
            && in_array($event->status, ['draft', 'rejected'], true);
    }

    public function approve(User $user, Event $event): bool
    {
        return $user->isRole('admin') && $event->status === 'pending';
    }
}
