<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Category;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\Registration;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrganizerController extends Controller
{
    public function dashboard(Request $request)
    {
        $events = $request->user()->events()->with('category')->latest()->limit(6)->get();
        $eventIds = $request->user()->events()->pluck('id');
        $stats = ['events' => $eventIds->count(), 'published' => $request->user()->events()->where('status', 'published')->count(), 'pending' => $request->user()->events()->where('status', 'pending')->count(), 'registrations' => Registration::whereIn('event_id', $eventIds)->where('status', 'registered')->count(), 'checkins' => Checkin::whereHas('ticket.registration', fn ($q) => $q->whereIn('event_id', $eventIds))->where('result', 'success')->count()];

        return view('organizer.dashboard', compact('events', 'stats'));
    }

    public function events(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $status = $request->input('status');
        $allowedStatuses = ['draft', 'pending', 'published', 'rejected', 'cancelled', 'completed'];
        $query = $request->user()->events()->with(['category', 'ticketTypes']);

        $query->when($search !== '', function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        });
        $query->when(in_array($status, $allowedStatuses, true), fn ($query) => $query->where('status', $status));

        $events = $query->latest()->paginate(10)->withQueryString();

        return view('organizer.events', compact('events'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('organizer.event-form', ['categories' => $categories, 'event' => null]);
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $ticketName = $data['ticket_name'];
        $ticketQuota = $data['ticket_quota'];
        unset($data['ticket_name'],$data['ticket_quota'],$data['poster']);
        $data['organizer_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['status'] = 'draft';
        if ($request->hasFile('poster')) {
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        } $event = DB::transaction(function () use ($data, $ticketName, $ticketQuota) {
            $event = Event::create($data);
            $event->ticketTypes()->create(['name' => $ticketName, 'quota' => $ticketQuota, 'description' => 'Tiket gratis Eventku']);

            return $event;
        });
        app(ActivityLogger::class)->log('event.created', $event, 'Event baru dibuat sebagai draft.');

        return redirect()->route('organizer.events')->with('success', 'Event berhasil disimpan sebagai draft.');
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);
        $event->load(['category', 'ticketTypes', 'registrations.user', 'registrations.digitalTicket']);

        return view('organizer.event-show', compact('event'));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('organizer.event-form', compact('event', 'categories'));
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $data = $request->validated();
        $ticketName = $data['ticket_name'];
        $ticketQuota = $data['ticket_quota'];
        unset($data['ticket_name'],$data['ticket_quota'],$data['poster']);
        $oldPoster = $event->poster_path;
        $newPoster = null;
        if ($request->hasFile('poster')) {
            $newPoster = $request->file('poster')->store('posters', 'public');
            $data['poster_path'] = $newPoster;
        } DB::transaction(function () use ($event, $data, $ticketName, $ticketQuota) {
            $event->update($data);
            $ticket = $event->ticketTypes()->first();
            if ($ticket) {
                $ticket->update(['name' => $ticketName, 'quota' => $ticketQuota]);
            } else {
                $event->ticketTypes()->create(['name' => $ticketName, 'quota' => $ticketQuota, 'description' => 'Tiket gratis Eventku']);
            }
        });
        if ($newPoster && $oldPoster && $oldPoster !== $newPoster) {
            Storage::disk('public')->delete($oldPoster);
        }

        return redirect()->route('organizer.events')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        if ($event->registrations()->exists()) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah memiliki data pendaftaran.');
        }

        $posterPath = $event->poster_path;

        DB::transaction(function () use ($event) {
            $event->ticketTypes()->delete();
            $event->delete();
        });

        if ($posterPath) {
            Storage::disk('public')->delete($posterPath);
        }

        app(ActivityLogger::class)->log('event.deleted', $event, 'Event draft dihapus oleh penyelenggara.');

        return redirect()->route('organizer.events')->with('success', 'Event berhasil dihapus.');
    }

    public function submit(Event $event)
    {
        $this->authorize('submit', $event);
        $event->update(['status' => 'pending', 'rejection_reason' => null]);
        app(ActivityLogger::class)->log('event.submitted', $event, 'Event diajukan untuk verifikasi admin.');

        return back()->with('success', 'Event diajukan untuk verifikasi.');
    }

    public function registrations(Request $request)
    {
        $events = $request->user()->events()->pluck('id');
        $registrations = Registration::with(['user', 'event', 'ticketType', 'digitalTicket'])->whereIn('event_id', $events)->latest()->paginate(15);

        return view('organizer.registrations', compact('registrations'));
    }

    public function checkin(Event $event)
    {
        $this->authorize('view', $event);

        return view('organizer.checkin', compact('event'));
    }

    public function reports(Request $request)
    {
        $events = $request->user()->events()->withCount(['registrations as registered_count' => fn ($q) => $q->where('status', 'registered')])->with(['ticketTypes'])->latest()->get();

        return view('organizer.reports', compact('events'));
    }
}
