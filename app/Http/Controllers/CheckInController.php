<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\CheckInService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class CheckInController extends Controller
{
    public function store(Request $request, Event $event): JsonResponse|RedirectResponse
    {
        $this->authorize('view', $event);

        $data = $request->validate([
            'code' => 'required|string|max:120',
            'method' => 'nullable|in:qr,manual',
        ]);

        try {
            $result = app(CheckInService::class)->validate(
                $request->user()->id,
                $event->id,
                $data['code'],
                $data['method'] ?? 'manual'
            );
        } catch (Throwable $exception) {
            report($exception);
            $result = [
                'success' => false,
                'code' => 'system_error',
                'message' => 'Terjadi gangguan saat memeriksa tiket. Silakan coba kembali.',
            ];
        }

        $presentedResult = $this->presentResult($result, $data['method'] ?? 'manual');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'result' => $presentedResult,
            ], $result['success'] ? 200 : 422);
        }

        $redirect = back()
            ->with('checkin_result', $presentedResult)
            ->with($result['success'] ? 'success' : 'error', $result['message']);

        if (! $result['success']) {
            $redirect->withInput(['code' => $data['code']]);
        }

        return $redirect;
    }

    private function presentResult(array $result, string $method): array
    {
        $messages = [
            'success' => [
                'title' => 'Check-in Berhasil',
                'message' => 'Tiket valid. Peserta berhasil dicatat hadir.',
                'tone' => 'success',
            ],
            'not_found' => [
                'title' => 'Tiket Tidak Ditemukan',
                'message' => 'Kode tiket tidak cocok. Periksa kembali kode pada tiket.',
                'tone' => 'error',
            ],
            'already_used' => [
                'title' => 'Peserta Sudah Check-in',
                'message' => 'Tiket ini sudah digunakan untuk check-in sebelumnya.',
                'tone' => 'warning',
            ],
            'cancelled' => [
                'title' => 'Tiket Tidak Berlaku',
                'message' => 'Tiket ini telah dibatalkan dan tidak dapat digunakan.',
                'tone' => 'error',
            ],
            'invalid_qr' => [
                'title' => 'QR Code Tidak Valid',
                'message' => 'QR Code tidak dapat diverifikasi. Pastikan kamu memindai tiket resmi Eventku.',
                'tone' => 'error',
            ],
            'wrong_event' => [
                'title' => 'Tiket Bukan untuk Event Ini',
                'message' => 'Tiket ini terdaftar untuk acara lain.',
                'tone' => 'error',
            ],
            'not_open' => [
                'title' => 'Check-in Belum Dibuka',
                'message' => 'Check-in belum tersedia untuk acara ini. Periksa jadwal check-in.',
                'tone' => 'warning',
            ],
            'closed' => [
                'title' => 'Check-in Ditutup',
                'message' => 'Check-in untuk acara ini sudah ditutup.',
                'tone' => 'warning',
            ],
            'system_error' => [
                'title' => 'Pemeriksaan Gagal',
                'message' => 'Terjadi gangguan saat memeriksa tiket. Silakan coba kembali.',
                'tone' => 'error',
            ],
        ];

        return array_merge($messages[$result['code']] ?? $messages['system_error'], [
            'code' => $result['code'],
            'success' => $result['success'],
            'participant_name' => $result['participant_name'] ?? null,
            'event_name' => $result['event_name'] ?? null,
            'checked_in_at' => $result['checked_in_at'] ?? null,
            'previous_checkin_at' => $result['previous_checkin_at'] ?? null,
            'method' => $method,
        ]);
    }
}
