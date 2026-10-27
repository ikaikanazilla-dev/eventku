<?php

namespace App\Http\Controllers;

use App\Models\DigitalTicket;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class TicketQrController extends Controller
{
    public function show(DigitalTicket $ticket)
    {
        $this->authorize('view', $ticket);
        $qr = new QrCode(data: $ticket->ticket_code, size: 300, margin: 10);
        $result = (new SvgWriter)->write($qr);

        return response($result->getString())->header('Content-Type', 'image/svg+xml');
    }
}
