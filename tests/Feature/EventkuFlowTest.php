<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DigitalTicket;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TicketType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventkuFlowTest extends TestCase
{
    use RefreshDatabase;

    private function event(array $overrides = []): array
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $category = Category::create(['name' => 'Teknologi', 'slug' => 'teknologi-'.fake()->unique()->numberBetween(1, 99999), 'is_active' => true]);
        $event = Event::create(array_merge(['organizer_id' => $organizer->id, 'category_id' => $category->id, 'name' => 'Test Event', 'slug' => 'test-event-'.fake()->unique()->numberBetween(1, 99999), 'description' => 'Deskripsi', 'location' => 'Jakarta', 'start_at' => now()->addHours(2), 'end_at' => now()->addHours(5), 'registration_start_at' => now()->subHour(), 'registration_end_at' => now()->addHour(), 'status' => 'published'], $overrides));
        $ticket = TicketType::create(['event_id' => $event->id, 'name' => 'General', 'quota' => 1]);

        return [$organizer, $event, $ticket];
    }

    private function checkinFixture(string $status = 'published'): array
    {
        $zone = 'Asia/Jakarta';
        $organizer = User::factory()->create(['role' => 'organizer']);
        $participant = User::factory()->create(['role' => 'participant']);
        $category = Category::create(['name' => 'Check-in', 'slug' => 'check-in-'.fake()->unique()->numberBetween(1, 99999), 'is_active' => true]);
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'category_id' => $category->id,
            'name' => 'Check-in Test Event',
            'slug' => 'check-in-test-'.fake()->unique()->numberBetween(1, 99999),
            'description' => 'Event untuk pengujian check-in.',
            'location' => 'Jakarta',
            'start_at' => Carbon::create(2026, 11, 20, 15, 0, 0, $zone),
            'end_at' => Carbon::create(2026, 11, 20, 17, 0, 0, $zone),
            'registration_start_at' => Carbon::create(2026, 11, 18, 0, 0, 0, $zone),
            'registration_end_at' => Carbon::create(2026, 11, 20, 14, 59, 59, $zone),
            'status' => $status,
        ]);
        $ticketType = TicketType::create(['event_id' => $event->id, 'name' => 'General', 'quota' => 1]);
        $registration = Registration::create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'registration_code' => 'REG-'.strtoupper(fake()->unique()->bothify('##########')),
            'status' => 'registered',
            'registered_at' => Carbon::create(2026, 11, 19, 12, 0, 0, $zone),
        ]);
        $digital = DigitalTicket::create([
            'registration_id' => $registration->id,
            'ticket_code' => 'EVK-TEST-'.strtoupper(fake()->unique()->bothify('########')),
            'qr_token_hash' => '',
            'status' => 'valid',
            'issued_at' => Carbon::create(2026, 11, 19, 12, 0, 0, $zone),
        ]);
        $digital->update(['qr_token_hash' => hash('sha256', $digital->ticket_code)]);

        return [$organizer, $event, $digital];
    }

    public function test_organizer_can_open_create_form_without_an_event_variable(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        Category::create(['name' => 'Teknologi', 'slug' => 'teknologi', 'is_active' => true]);

        $this->actingAs($organizer)
            ->get(route('organizer.event.create'))
            ->assertOk()
            ->assertSee('Buat Event Baru');
    }

    public function test_organizer_can_create_event_with_ticket_type(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer']);
        $category = Category::create(['name' => 'Konferensi', 'slug' => 'konferensi', 'is_active' => true]);
        $payload = [
            'category_id' => $category->id,
            'name' => 'Event Baru Organizer',
            'description' => 'Deskripsi event baru.',
            'location' => 'Jakarta',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i'),
            'end_at' => now()->addDays(3)->addHours(3)->format('Y-m-d H:i'),
            'registration_start_at' => now()->addDay()->format('Y-m-d H:i'),
            'registration_end_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'ticket_name' => 'General Pass',
            'ticket_quota' => 250,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.event.store'), $payload);

        $response->assertRedirect(route('organizer.events'));
        $this->assertDatabaseHas('events', ['organizer_id' => $organizer->id, 'name' => 'Event Baru Organizer', 'status' => 'draft']);
        $this->assertDatabaseHas('ticket_types', ['name' => 'General Pass', 'quota' => 250]);
    }

    public function test_invalid_event_payload_is_rejected_before_persisting(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer']);

        $this->actingAs($organizer)
            ->post(route('organizer.event.store'), ['name' => 'Tidak lengkap'])
            ->assertSessionHasErrors(['category_id', 'description', 'start_at', 'ticket_name', 'ticket_quota']);

        $this->assertDatabaseCount('events', 0);
    }

    public function test_organizer_can_edit_event_and_update_ticket_details(): void
    {
        [$organizer,$event] = $this->event(['status' => 'draft']);
        $payload = [
            'category_id' => $event->category_id,
            'name' => 'Event Diperbarui',
            'description' => 'Deskripsi yang diperbarui.',
            'location' => 'Bandung',
            'start_at' => now()->addDays(4)->format('Y-m-d H:i'),
            'end_at' => now()->addDays(4)->addHours(3)->format('Y-m-d H:i'),
            'registration_start_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'registration_end_at' => now()->addDays(3)->format('Y-m-d H:i'),
            'ticket_name' => 'VIP Pass',
            'ticket_quota' => 80,
        ];

        $this->actingAs($organizer)->get(route('organizer.event.edit', $event))->assertOk()->assertSee('Edit Event');
        $this->actingAs($organizer)->put(route('organizer.event.update', $event), $payload)->assertRedirect(route('organizer.events'));

        $this->assertDatabaseHas('events', ['id' => $event->id, 'name' => 'Event Diperbarui', 'location' => 'Bandung']);
        $this->assertDatabaseHas('ticket_types', ['event_id' => $event->id, 'name' => 'VIP Pass', 'quota' => 80]);
    }

    public function test_organizer_can_replace_event_poster(): void
    {
        Storage::fake('public');
        [$organizer,$event] = $this->event(['status' => 'draft']);
        $payload = [
            'category_id' => $event->category_id,
            'name' => $event->name,
            'description' => $event->description,
            'location' => $event->location,
            'start_at' => $event->start_at->format('Y-m-d H:i'),
            'end_at' => $event->end_at->format('Y-m-d H:i'),
            'registration_start_at' => $event->registration_start_at->format('Y-m-d H:i'),
            'registration_end_at' => $event->registration_end_at->format('Y-m-d H:i'),
            'ticket_name' => 'General',
            'ticket_quota' => 1,
            'poster' => UploadedFile::fake()->image('poster.png'),
        ];

        $this->actingAs($organizer)->put(route('organizer.event.update', $event), $payload)->assertRedirect();

        $event->refresh();
        $oldPoster = $event->poster_path;
        $this->assertNotNull($oldPoster);
        Storage::disk('public')->assertExists($oldPoster);

        $payload['poster'] = UploadedFile::fake()->image('poster-replacement.png');
        $this->actingAs($organizer)->put(route('organizer.event.update', $event), $payload)->assertRedirect();

        $event->refresh();
        $this->assertNotSame($oldPoster, $event->poster_path);
        Storage::disk('public')->assertMissing($oldPoster);
        Storage::disk('public')->assertExists($event->poster_path);
    }

    public function test_invalid_poster_is_rejected(): void
    {
        [$organizer,$event] = $this->event(['status' => 'draft']);
        $payload = [
            'category_id' => $event->category_id,
            'name' => $event->name,
            'description' => $event->description,
            'location' => $event->location,
            'start_at' => now()->addDays(4)->format('Y-m-d H:i'),
            'end_at' => now()->addDays(4)->addHours(3)->format('Y-m-d H:i'),
            'registration_start_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'registration_end_at' => now()->addDays(3)->format('Y-m-d H:i'),
            'ticket_name' => 'General',
            'ticket_quota' => 1,
            'poster' => UploadedFile::fake()->create('poster.pdf', 100, 'application/pdf'),
        ];

        $this->actingAs($organizer)
            ->put(route('organizer.event.update', $event), $payload)
            ->assertSessionHasErrors('poster');
    }

    public function test_organizer_can_filter_and_delete_an_unregistered_draft_event(): void
    {
        [$organizer, $event, $ticket] = $this->event(['status' => 'draft', 'name' => 'Workshop Aman']);

        $this->actingAs($organizer)
            ->get(route('organizer.events', ['q' => 'Workshop Aman', 'status' => 'draft']))
            ->assertOk()
            ->assertSee('Workshop Aman')
            ->assertSee('<strong>1</strong>', false);

        $this->actingAs($organizer)
            ->delete(route('organizer.event.destroy', $event))
            ->assertRedirect(route('organizer.events'));

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        $this->assertDatabaseMissing('ticket_types', ['id' => $ticket->id]);
    }

    public function test_event_with_registration_cannot_be_deleted(): void
    {
        [$organizer, $event, $ticket] = $this->event(['status' => 'draft']);
        $participant = User::factory()->create(['role' => 'participant']);
        Registration::create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'ticket_type_id' => $ticket->id,
            'registration_code' => 'REG-'.strtoupper(fake()->unique()->bothify('##########')),
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        $this->actingAs($organizer)
            ->delete(route('organizer.event.destroy', $event))
            ->assertRedirect()
            ->assertSessionHas('error', 'Event tidak dapat dihapus karena sudah memiliki data pendaftaran.');

        $this->assertDatabaseHas('events', ['id' => $event->id]);
        $this->assertDatabaseHas('ticket_types', ['id' => $ticket->id]);
    }

    public function test_published_event_cannot_be_edited_or_deleted(): void
    {
        [$organizer, $event] = $this->event(['status' => 'published']);

        $this->actingAs($organizer)->get(route('organizer.event.edit', $event))->assertForbidden();
        $this->actingAs($organizer)->delete(route('organizer.event.destroy', $event))->assertForbidden();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'status' => 'published']);
    }

    public function test_participant_registration_issues_one_ticket(): void
    {
        [$organizer,$event,$ticket] = $this->event();
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->post(route('participant.event.register', $event), ['ticket_type_id' => $ticket->id])->assertRedirect();
        $this->assertDatabaseHas('registrations', ['user_id' => $participant->id, 'event_id' => $event->id, 'status' => 'registered']);
        $this->assertDatabaseCount('digital_tickets', 1);
    }

    public function test_duplicate_registration_and_quota_are_rejected(): void
    {
        [, $event, $ticket] = $this->event();
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->post(route('participant.event.register', $event), ['ticket_type_id' => $ticket->id]);
        $this->actingAs($participant)->post(route('participant.event.register', $event), ['ticket_type_id' => $ticket->id])->assertSessionHasErrors('event');
        $other = User::factory()->create(['role' => 'participant']);
        $this->actingAs($other)->post(route('participant.event.register', $event), ['ticket_type_id' => $ticket->id])->assertSessionHasErrors('ticket_type');
    }

    public function test_organizer_can_check_in_once_and_second_attempt_is_rejected(): void
    {
        [$organizer,$event,$ticket] = $this->event();
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->post(route('participant.event.register', $event), ['ticket_type_id' => $ticket->id]);
        $digital = DigitalTicket::first();
        $this->actingAs($organizer)->post(route('organizer.checkin.store', $event), ['code' => $digital->ticket_code, 'method' => 'manual'])->assertSessionHas('success')->assertSessionHas('checkin_result', fn ($result) => $result['code'] === 'success');
        $this->actingAs($organizer)->post(route('organizer.checkin.store', $event), ['code' => $digital->ticket_code, 'method' => 'manual'])->assertSessionHas('error')->assertSessionHas('checkin_result', fn ($result) => $result['code'] === 'already_used');
        $this->assertDatabaseHas('digital_tickets', ['id' => $digital->id, 'status' => 'used']);
        $this->assertDatabaseCount('checkins', 2);
    }

    public function test_qr_checkin_returns_a_json_result_for_the_scanner(): void
    {
        [$organizer,$event,$digital] = $this->checkinFixture();
        Carbon::setTestNow(Carbon::create(2026, 11, 20, 12, 0, 0, 'Asia/Jakarta'));

        try {
            $this->actingAs($organizer)
                ->withHeader('Accept', 'application/json')
                ->post(route('organizer.checkin.store', $event), [
                    'code' => $digital->ticket_code,
                    'method' => 'qr',
                ])
                ->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('result.code', 'success')
                ->assertJsonPath('result.title', 'Check-in Berhasil');

            $this->assertDatabaseHas('digital_tickets', ['id' => $digital->id, 'status' => 'used']);
            $this->assertDatabaseHas('checkins', ['digital_ticket_id' => $digital->id, 'method' => 'qr', 'result' => 'success']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_qr_checkin_json_distinguishes_a_missing_ticket_from_decode_failure(): void
    {
        [$organizer,$event] = $this->checkinFixture();
        Carbon::setTestNow(Carbon::create(2026, 11, 20, 12, 0, 0, 'Asia/Jakarta'));

        try {
            $this->actingAs($organizer)
                ->withHeader('Accept', 'application/json')
                ->post(route('organizer.checkin.store', $event), [
                    'code' => 'EVK-NOT-VALID',
                    'method' => 'qr',
                ])
                ->assertUnprocessable()
                ->assertJsonPath('success', false)
                ->assertJsonPath('result.code', 'not_found')
                ->assertJsonPath('result.title', 'Tiket Tidak Ditemukan');

            $this->assertDatabaseCount('checkins', 0);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_checkin_screen_uses_clear_indonesian_labels(): void
    {
        [$organizer,$event] = $this->event();

        $this->actingAs($organizer)
            ->get(route('organizer.checkin', $event))
            ->assertOk()
            ->assertSee('Check-in Peserta')
            ->assertSee('Mulai Scan QR')
            ->assertSee('Hentikan Scan')
            ->assertSee('Masukkan Kode Tiket')
            ->assertSee('Periksa Tiket')
            ->assertSee('Kamera siap digunakan')
            ->assertSee('data-checkin-autostart="true"', false)
            ->assertSee('Menghubungkan kamera...', false)
            ->assertSee('QR BERHASIL TERBACA', false)
            ->assertSee('Mencari QR...', false)
            ->assertSee('PEMERIKSAAN TIKET OTOMATIS')
            ->assertDontSee('Validasi berlapis')
            ->assertDontSee('transaction lock');
    }

    public function test_checkin_time_boundaries_are_enforced_without_using_machine_time(): void
    {
        $cases = [
            ['before', Carbon::create(2026, 11, 20, 8, 59, 59, 'Asia/Jakarta'), false],
            ['opening', Carbon::create(2026, 11, 20, 9, 0, 0, 'Asia/Jakarta'), true],
            ['during', Carbon::create(2026, 11, 20, 12, 0, 0, 'Asia/Jakarta'), true],
            ['closing', Carbon::create(2026, 11, 20, 17, 0, 0, 'Asia/Jakarta'), true],
            ['after', Carbon::create(2026, 11, 20, 17, 0, 1, 'Asia/Jakarta'), false],
        ];

        foreach ($cases as [$label,$at,$allowed]) {
            [$organizer,$event,$digital] = $this->checkinFixture();
            Carbon::setTestNow($at);

            try {
                $response = $this->actingAs($organizer)->post(route('organizer.checkin.store', $event), [
                    'code' => $digital->ticket_code,
                    'method' => 'manual',
                ]);

                if ($allowed) {
                    $response->assertSessionHas('success');
                    $this->assertDatabaseHas('checkins', ['digital_ticket_id' => $digital->id, 'result' => 'success']);
                } else {
                    $response->assertSessionHas('error', 'Check-in belum atau sudah tidak tersedia untuk event ini.');
                    $this->assertDatabaseHas('checkins', ['digital_ticket_id' => $digital->id, 'result' => 'rejected']);
                }
            } finally {
                Carbon::setTestNow();
            }
        }
    }

    public function test_checkin_requires_published_event_but_not_full_quota(): void
    {
        [$organizer,$event,$digital] = $this->checkinFixture('pending');
        Carbon::setTestNow(Carbon::create(2026, 11, 20, 12, 0, 0, 'Asia/Jakarta'));

        try {
            $this->actingAs($organizer)
                ->post(route('organizer.checkin.store', $event), ['code' => $digital->ticket_code, 'method' => 'manual'])
                ->assertSessionHas('error', 'Check-in belum atau sudah tidak tersedia untuk event ini.');
            $this->assertDatabaseHas('digital_tickets', ['id' => $digital->id, 'status' => 'valid']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_invalid_and_already_used_tickets_are_rejected(): void
    {
        [$organizer,$event,$digital] = $this->checkinFixture();
        $at = Carbon::create(2026, 11, 20, 12, 0, 0, 'Asia/Jakarta');
        Carbon::setTestNow($at);

        try {
            $this->actingAs($organizer)
                ->post(route('organizer.checkin.store', $event), ['code' => 'EVK-NOT-VALID', 'method' => 'manual'])
                ->assertSessionHas('error', 'Kode tiket tidak ditemukan.')
                ->assertSessionHas('checkin_result', fn ($result) => $result['code'] === 'not_found');
            $this->assertDatabaseCount('checkins', 0);

            $this->actingAs($organizer)
                ->post(route('organizer.checkin.store', $event), ['code' => $digital->ticket_code, 'method' => 'manual'])
                ->assertSessionHas('success');
            Carbon::setTestNow($at->copy()->addSecond());
            $this->actingAs($organizer)
                ->post(route('organizer.checkin.store', $event), ['code' => $digital->ticket_code, 'method' => 'manual'])
                ->assertSessionHas('error', 'Tiket sudah digunakan.');
            $this->assertDatabaseCount('checkins', 2);
            $this->assertDatabaseHas('digital_tickets', ['id' => $digital->id, 'status' => 'used']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_cancelled_invalid_qr_and_wrong_event_results_are_mapped_for_staff(): void
    {
        [$organizer,$event,$digital] = $this->checkinFixture();
        Carbon::setTestNow(Carbon::create(2026, 11, 20, 12, 0, 0, 'Asia/Jakarta'));

        try {
            $digital->update(['status' => 'cancelled']);
            $this->actingAs($organizer)
                ->post(route('organizer.checkin.store', $event), ['code' => $digital->ticket_code, 'method' => 'manual'])
                ->assertSessionHas('checkin_result', fn ($result) => $result['code'] === 'cancelled');

            [$sameOrganizer,$sameEvent,$invalidQr] = $this->checkinFixture();
            $invalidQr->update(['qr_token_hash' => 'invalid-hash']);
            $this->actingAs($sameOrganizer)
                ->post(route('organizer.checkin.store', $sameEvent), ['code' => $invalidQr->ticket_code, 'method' => 'qr'])
                ->assertSessionHas('checkin_result', fn ($result) => $result['code'] === 'invalid_qr');

            [$owner,$ownerEvent] = $this->checkinFixture();
            [,,$otherEventTicket] = $this->checkinFixture();
            $this->actingAs($owner)
                ->post(route('organizer.checkin.store', $ownerEvent), ['code' => $otherEventTicket->ticket_code, 'method' => 'qr'])
                ->assertSessionHas('checkin_result', fn ($result) => $result['code'] === 'wrong_event');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_checkin_uses_a_consistent_indonesia_timezone(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
        [$organizer,$event,$digital] = $this->checkinFixture();
        Carbon::setTestNow(Carbon::create(2026, 11, 20, 5, 0, 0, 'UTC'));

        try {
            $this->actingAs($organizer)
                ->post(route('organizer.checkin.store', $event), ['code' => $digital->ticket_code, 'method' => 'manual'])
                ->assertSessionHas('success');
            $this->assertDatabaseHas('checkins', ['digital_ticket_id' => $digital->id, 'result' => 'success']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_participant_cannot_access_admin_dashboard(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_ticket_qr_and_pdf_are_owner_protected(): void
    {
        [, $event, $ticket] = $this->event();
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->post(route('participant.event.register', $event), ['ticket_type_id' => $ticket->id]);
        $digital = DigitalTicket::first();
        $this->actingAs($participant)->get(route('participant.ticket.qr', $digital))->assertOk()->assertHeader('content-type', 'image/svg+xml');
        $this->actingAs($participant)->get(route('participant.ticket.pdf', $digital))->assertOk()->assertHeader('content-type', 'application/pdf');
        $other = User::factory()->create(['role' => 'participant']);
        $this->actingAs($other)->get(route('participant.ticket.pdf', $digital))->assertForbidden();
    }

    public function test_inactive_account_cannot_login(): void
    {
        $user = User::factory()->create(['email' => 'inactive@example.test', 'status' => 'inactive']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
    }

    public function test_organizer_cannot_manage_another_organizers_event(): void
    {
        [$owner,$event] = $this->event();
        $other = User::factory()->create(['role' => 'organizer']);
        $this->actingAs($other)->get(route('organizer.event.show', $event))->assertForbidden();
        $this->actingAs($other)->get(route('organizer.checkin', $event))->assertForbidden();
        $this->actingAs($other)->get(route('organizer.event.edit', $event))->assertForbidden();
        $this->actingAs($other)->put(route('organizer.event.update', $event), [])->assertForbidden();
        $this->actingAs($owner)->get(route('organizer.event.show', $event))->assertOk();
    }

    public function test_admin_can_approve_pending_event(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [, $event] = $this->event(['status' => 'pending']);
        $this->actingAs($admin)->post(route('admin.event.approve', $event))->assertSessionHas('success');
        $this->assertDatabaseHas('events', ['id' => $event->id, 'status' => 'published', 'verified_by' => $admin->id]);
    }

    public function test_admin_can_view_event_detail_and_cannot_review_non_pending_event(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [, $pending] = $this->event(['status' => 'pending']);
        [, $published] = $this->event(['status' => 'published']);

        $this->actingAs($admin)->get(route('admin.event.show', $pending))->assertOk()->assertSee($pending->name);
        $this->actingAs($admin)->post(route('admin.event.approve', $published))->assertForbidden();
    }

    public function test_admin_can_edit_published_event_schedule_without_recreating_tickets_or_registrations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$organizer, $event, $ticket] = $this->event(['status' => 'published']);
        $participant = User::factory()->create(['role' => 'participant']);
        $registration = Registration::create([
            'user_id' => $participant->id,
            'event_id' => $event->id,
            'ticket_type_id' => $ticket->id,
            'registration_code' => 'REG-'.strtoupper(fake()->unique()->bothify('##########')),
            'status' => 'registered',
            'registered_at' => now(),
        ]);
        $newStart = now()->addDays(4)->setTime(15, 0);
        $newEnd = $newStart->copy()->addHours(3);
        $payload = [
            'category_id' => $event->category_id,
            'name' => $event->name,
            'description' => $event->description,
            'location' => $event->location,
            'start_at' => $newStart->format('Y-m-d H:i'),
            'end_at' => $newEnd->format('Y-m-d H:i'),
            'registration_start_at' => now()->addDay()->format('Y-m-d H:i'),
            'registration_end_at' => now()->addDays(3)->format('Y-m-d H:i'),
            'ticket_name' => $ticket->name,
            'ticket_quota' => $ticket->quota,
        ];

        $this->actingAs($admin)
            ->get(route('admin.event.edit', $event))
            ->assertOk()
            ->assertSee('Simpan perubahan')
            ->assertSee($event->start_at->format('Y-m-d\\TH:i'));

        $this->actingAs($admin)
            ->put(route('admin.event.update', $event), $payload)
            ->assertRedirect(route('admin.event.show', $event))
            ->assertSessionHas('success', 'Event berhasil diperbarui.');

        $event->refresh();
        $this->assertSame($newStart->format('Y-m-d H:i'), $event->start_at->format('Y-m-d H:i'));
        $this->assertSame($newEnd->format('Y-m-d H:i'), $event->end_at->format('Y-m-d H:i'));
        $this->assertSame('published', $event->status);
        $this->assertDatabaseHas('registrations', ['id' => $registration->id, 'event_id' => $event->id, 'ticket_type_id' => $ticket->id]);
        $this->assertDatabaseHas('ticket_types', ['id' => $ticket->id, 'event_id' => $event->id]);
        $this->assertDatabaseHas('activity_logs', ['action' => 'event.updated', 'entity_id' => $event->id, 'user_id' => $admin->id]);
        $this->assertSame(1, Event::where('organizer_id', $organizer->id)->count());
        $this->assertSame(1, TicketType::where('event_id', $event->id)->count());

        $this->actingAs($admin)
            ->get(route('admin.event.show', $event))
            ->assertOk()
            ->assertSee($newStart->format('d M Y H:i'));
        $this->actingAs($admin)
            ->get(route('admin.events'))
            ->assertOk()
            ->assertSee($newStart->format('d M Y H:i'));
        $this->actingAs($organizer)
            ->get(route('organizer.dashboard'))
            ->assertOk()
            ->assertSee($newStart->format('d M Y'));
        $this->get(route('events.show', $event->slug))
            ->assertOk()
            ->assertSee($newStart->format('d M Y'))
            ->assertSee($newStart->format('H:i').' WIB');
    }

    public function test_admin_event_schedule_rejects_end_time_before_or_equal_to_start_time(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [, $event, $ticket] = $this->event(['status' => 'published']);
        $start = now()->addDays(4)->setTime(15, 0);
        $payload = [
            'category_id' => $event->category_id,
            'name' => $event->name,
            'description' => $event->description,
            'location' => $event->location,
            'start_at' => $start->format('Y-m-d H:i'),
            'end_at' => $start->format('Y-m-d H:i'),
            'registration_start_at' => now()->addDay()->format('Y-m-d H:i'),
            'registration_end_at' => now()->addDays(3)->format('Y-m-d H:i'),
            'ticket_name' => $ticket->name,
            'ticket_quota' => $ticket->quota,
        ];

        $this->actingAs($admin)
            ->put(route('admin.event.update', $event), $payload)
            ->assertSessionHasErrors('end_at');

        $this->assertDatabaseHas('events', ['id' => $event->id, 'status' => 'published', 'name' => $event->name]);
    }

    public function test_organizer_and_participant_cannot_access_admin_event_edit(): void
    {
        [$organizer, $event] = $this->event(['status' => 'published']);
        $participant = User::factory()->create(['role' => 'participant']);

        $this->actingAs($organizer)->get(route('admin.event.edit', $event))->assertForbidden();
        $this->actingAs($organizer)->put(route('admin.event.update', $event), [])->assertForbidden();
        $this->actingAs($participant)->get(route('admin.event.edit', $event))->assertForbidden();
        $this->actingAs($participant)->put(route('admin.event.update', $event), [])->assertForbidden();
    }

    public function test_admin_pages_share_the_same_sidebar_and_render_their_real_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [, $event] = $this->event(['status' => 'pending']);
        $pages = [
            route('admin.dashboard') => 'Dashboard admin',
            route('admin.events') => 'Review event',
            route('admin.event.show', $event) => $event->name,
            route('admin.categories') => 'Kategori event',
            route('admin.users') => 'Pengguna',
            route('admin.logs') => 'Log aktivitas',
        ];

        foreach ($pages as $url => $content) {
            $this->actingAs($admin)
                ->get($url)
                ->assertOk()
                ->assertSee('admin-sidebar')
                ->assertSee('Moderasi Event')
                ->assertSee('Log Aktivitas')
                ->assertSee($content);
        }
    }

    public function test_admin_can_manage_categories_and_user_status_or_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'participant']);

        $this->actingAs($admin)->get(route('admin.categories'))->assertOk();
        $this->actingAs($admin)->post(route('admin.category.store'), ['name' => 'Bisnis', 'description' => 'Kategori bisnis'])
            ->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Bisnis']);
        $this->actingAs($admin)->patch(route('admin.user.role', $user), ['role' => 'organizer'])->assertRedirect();
        $this->actingAs($admin)->patch(route('admin.user.toggle', $user))->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'organizer', 'status' => 'inactive']);
    }

    public function test_guest_and_wrong_role_cannot_use_protected_crud_routes(): void
    {
        $this->get(route('organizer.event.create'))->assertRedirect(route('login'));
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->get(route('organizer.event.create'))->assertForbidden();
        $this->actingAs($participant)->get(route('admin.categories'))->assertForbidden();
    }

    public function test_role_dashboards_and_operational_pages_render(): void
    {
        [$organizer,$event,$ticket] = $this->event();
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->post(route('participant.event.register', $event), ['ticket_type_id' => $ticket->id]);
        $admin = User::factory()->create(['role' => 'admin']);

        foreach ([
            route('organizer.dashboard'),
            route('organizer.events'),
            route('organizer.event.show', $event),
            route('organizer.checkin', $event),
            route('organizer.reports'),
        ] as $url) {
            $this->actingAs($organizer)->get($url)->assertOk();
        }

        foreach ([
            route('admin.dashboard'),
            route('admin.events'),
            route('admin.event.show', $event),
            route('admin.categories'),
            route('admin.logs'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $digital = DigitalTicket::first();
        $this->actingAs($participant)->get(route('participant.dashboard'))->assertOk();
        $this->actingAs($participant)->get(route('participant.tickets'))->assertOk();
        $this->actingAs($participant)->get(route('participant.ticket.show', $digital))->assertOk();
    }

    public function test_public_event_cards_use_local_category_fallback_images(): void
    {
        [, $event] = $this->event();
        $response = $this->get(route('home'));
        $response->assertOk()->assertSee('event-card')->assertSee('images/events/seminar-teknologi.svg');
    }

    public function test_event_posters_use_a_direct_public_upload_url(): void
    {
        $posterPath = 'events/direct-public-poster.jpg';
        Storage::disk('public')->put($posterPath, 'poster');

        try {
            [, $event] = $this->event(['poster_path' => $posterPath]);

            $this->assertSame('/uploads/events/direct-public-poster.jpg', $event->poster_url);
        } finally {
            Storage::disk('public')->delete($posterPath);
        }
    }

    public function test_navigation_changes_for_guest_and_authenticated_roles(): void
    {
        $this->get(route('home'))->assertSee('mobile-toggle')->assertSee('Daftar Gratis');
        $organizer = User::factory()->create(['role' => 'organizer']);
        $this->actingAs($organizer)->get(route('home'))->assertSee('Dashboard')->assertSee('profile-menu');
        $participant = User::factory()->create(['role' => 'participant']);
        $this->actingAs($participant)->get(route('home'))->assertSee('Tiket Saya')->assertSee('profile-menu');
    }
}
