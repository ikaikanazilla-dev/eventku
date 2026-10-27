<?php

namespace App\Services;

use App\Models\DigitalTicket;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    public function register(int $userId, int $eventId, int $ticketTypeId): Registration
    {
        return DB::transaction(function () use ($userId, $eventId, $ticketTypeId) {
            $event = Event::query()->lockForUpdate()->findOrFail($eventId);
            if (! $event->isRegistrationOpen()) {
                throw ValidationException::withMessages(['event' => 'Pendaftaran event sedang tidak dibuka.']);
            }
            $ticketType = TicketType::query()->where('event_id', $event->id)->lockForUpdate()->find($ticketTypeId);
            if (! $ticketType) {
                throw ValidationException::withMessages(['ticket_type' => 'Jenis tiket tidak tersedia untuk event ini.']);
            }
            if (Registration::where(['user_id' => $userId, 'event_id' => $event->id])->where('status', 'registered')->exists()) {
                throw ValidationException::withMessages(['event' => 'Anda sudah memiliki pendaftaran aktif untuk event ini.']);
            }
            if ($ticketType->registrations()->where('status', 'registered')->count() >= $ticketType->quota) {
                throw ValidationException::withMessages(['ticket_type' => 'Kuota tiket ini sudah habis.']);
            }
            $registration = Registration::create(['user_id' => $userId, 'event_id' => $event->id, 'ticket_type_id' => $ticketType->id, 'registration_code' => 'REG-'.strtoupper(Str::random(10)), 'status' => 'registered', 'registered_at' => now()]);
            $ticketCode = 'EVK-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(8));
            DigitalTicket::create(['registration_id' => $registration->id, 'ticket_code' => $ticketCode, 'qr_token_hash' => hash('sha256', $ticketCode), 'status' => 'valid', 'issued_at' => now()]);
            app(ActivityLogger::class)->log('registration.created', $registration, 'Pendaftaran peserta dan tiket digital diterbitkan.');

            return $registration->load(['event', 'ticketType', 'digitalTicket']);
        }, 3);
    }
}
