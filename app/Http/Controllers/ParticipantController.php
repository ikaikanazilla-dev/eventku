<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterEventRequest;
use App\Models\DigitalTicket;
use App\Models\Event;
use App\Services\RegistrationService;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function dashboard(Request $request)
    {
        $registrations = $request->user()->registrations()->with(['event', 'digitalTicket'])->where('status', 'registered')->latest()->limit(4)->get();
        $stats = ['valid' => $request->user()->registrations()->whereHas('digitalTicket', fn ($q) => $q->where('status', 'valid'))->count(), 'used' => $request->user()->registrations()->whereHas('digitalTicket', fn ($q) => $q->where('status', 'used'))->count()];

        return view('participant.dashboard', compact('registrations', 'stats'));
    }

    public function tickets(Request $request)
    {
        $registrations = $request->user()->registrations()->with(['event.category', 'ticketType', 'digitalTicket'])->latest()->paginate(8);

        return view('participant.tickets', compact('registrations'));
    }

    public function register(RegisterEventRequest $request, Event $event)
    {
        $registration = app(RegistrationService::class)->register($request->user()->id, $event->id, (int) $request->ticket_type_id);

        return redirect()->route('participant.ticket.show', $registration->digitalTicket)->with('success', 'Pendaftaran berhasil. Tiket digital telah diterbitkan.');
    }

    public function ticket(DigitalTicket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['registration.event.category', 'registration.ticketType', 'registration.user']);

        return view('participant.ticket', compact('ticket'));
    }

    public function pdf(DigitalTicket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['registration.event.category', 'registration.ticketType', 'registration.user']);
        $pdf = new \TCPDF;
        $pdf->SetCreator('Eventku');
        $pdf->SetAuthor('Eventku Digital Platform');
        $pdf->SetTitle('Tiket '.$ticket->ticket_code);
        $pdf->SetMargins(18, 18, 18);
        $pdf->AddPage();
        $html = '<h1 style="color:#142D4E">EVENTKU · TIKET DIGITAL</h1><h2>'.e($ticket->registration->event->name).'</h2><p>'.e($ticket->registration->event->start_at->format('d M Y, H:i')).' WIB · '.e($ticket->registration->event->location).'</p><hr><table cellpadding="7"><tr><td><b>Peserta</b><br>'.e($ticket->registration->user->name).'</td><td><b>Jenis Tiket</b><br>'.e($ticket->registration->ticketType->name).'</td></tr><tr><td><b>Kode Tiket</b><br>'.e($ticket->ticket_code).'</td><td><b>Status</b><br>'.e(ucfirst($ticket->status)).'</td></tr></table><p>Tunjukkan QR ini di gate. Tiket hanya dapat digunakan satu kali.</p>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->write2DBarcode($ticket->ticket_code, 'QRCODE,H', 70, 115, 70, 70);
        $filename = 'eventku-'.$ticket->ticket_code.'.pdf';

        return response($pdf->Output($filename, 'S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="'.$filename.'"']);
    }
}
