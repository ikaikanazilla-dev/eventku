<?php

namespace App\Services;

use App\Models\Checkin;
use App\Models\DigitalTicket;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class CheckInService
{
    public function validate(int $staffId, int $eventId, string $code, string $method = 'manual'): array
    {
        return DB::transaction(function () use ($staffId, $eventId, $code, $method) {
            $normalizedCode = strtoupper(trim($code));
            $ticket = DigitalTicket::query()
                ->where('ticket_code', $normalizedCode)
                ->lockForUpdate()
                ->first();

            if (! $ticket) {
                return $this->rejected(null, $staffId, $method, 'not_found', 'Kode tiket tidak ditemukan.');
            }

            $registration = $ticket->registration()->with(['event', 'user'])->first();

            if (! $registration || ! hash_equals($ticket->qr_token_hash, hash('sha256', $normalizedCode))) {
                return $this->rejected($ticket, $staffId, $method, 'invalid_qr', 'Token tiket tidak valid.');
            }

            if ((int) $registration->event_id !== $eventId) {
                return $this->rejected($ticket, $staffId, $method, 'wrong_event', 'Tiket ini untuk event yang berbeda.');
            }

            if ($ticket->status === 'used') {
                $previousCheckin = $ticket->checkins()
                    ->where('result', 'success')
                    ->latest('checked_in_at')
                    ->first();

                return $this->rejected(
                    $ticket,
                    $staffId,
                    $method,
                    'already_used',
                    'Tiket sudah digunakan.',
                    ['previous_checkin_at' => $previousCheckin?->checked_in_at?->toIso8601String()]
                );
            }

            if ($ticket->status === 'cancelled' || $registration->status === 'cancelled') {
                return $this->rejected($ticket, $staffId, $method, 'cancelled', 'Tiket telah dibatalkan.');
            }

            $event = Event::find($eventId);
            $checkedInAt = now();

            if (! $event || $event->status !== 'published' || $checkedInAt->lt($event->start_at->copy()->subHours(6))) {
                return $this->rejected($ticket, $staffId, $method, 'not_open', 'Check-in belum atau sudah tidak tersedia untuk event ini.');
            }

            if ($checkedInAt->gt($event->end_at)) {
                return $this->rejected($ticket, $staffId, $method, 'closed', 'Check-in belum atau sudah tidak tersedia untuk event ini.');
            }

            $ticket->update(['status' => 'used', 'used_at' => $checkedInAt]);
            Checkin::create([
                'digital_ticket_id' => $ticket->id,
                'successful_ticket_id' => $ticket->id,
                'checked_in_by' => $staffId,
                'checked_in_at' => $checkedInAt,
                'method' => $method,
                'result' => 'success',
            ]);

            app(ActivityLogger::class)->log('checkin.success', $ticket, 'Check-in tiket berhasil.');

            return [
                'success' => true,
                'code' => 'success',
                'message' => 'Check-in berhasil. Tiket valid dan tercatat.',
                'participant_name' => $registration->user?->name,
                'event_name' => $event->name,
                'checked_in_at' => $checkedInAt->toIso8601String(),
            ];
        }, 3);
    }

    private function rejected(
        ?DigitalTicket $ticket,
        int $staffId,
        string $method,
        string $code,
        string $message,
        array $extra = []
    ): array {
        if ($ticket) {
            Checkin::create([
                'digital_ticket_id' => $ticket->id,
                'checked_in_by' => $staffId,
                'checked_in_at' => now(),
                'method' => $method,
                'result' => 'rejected',
                'notes' => $message,
            ]);
        }

        return array_merge([
            'success' => false,
            'code' => $code,
            'message' => $message,
        ], $extra);
    }
}
