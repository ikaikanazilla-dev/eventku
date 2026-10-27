<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Checkin;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = ['users' => User::count(), 'organizers' => User::where('role', 'organizer')->count(), 'participants' => User::where('role', 'participant')->count(), 'events' => Event::count(), 'pending' => Event::where('status', 'pending')->count(), 'registrations' => Registration::where('status', 'registered')->count(), 'checkins' => Checkin::where('result', 'success')->count()];
        $events = Event::with('organizer')->where('status', 'pending')->latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'events'));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function toggleUser(User $user)
    {
        abort_if($user->is(auth()->user()), 403);
        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
        app(ActivityLogger::class)->log('user.status_changed', $user, 'Status akun diubah admin.');

        return back()->with('success', 'Status akun diperbarui.');
    }

    public function updateRole(Request $request, User $user)
    {
        abort_if($user->is(auth()->user()), 403);
        $data = $request->validate(['role' => 'required|in:admin,organizer,participant']);
        $user->update(['role' => $data['role']]);
        app(ActivityLogger::class)->log('user.role_changed', $user, 'Role akun diubah menjadi '.$data['role'].'.');

        return back()->with('success', 'Role pengguna diperbarui.');
    }

    public function events(Request $request)
    {
        $events = Event::with(['organizer', 'category'])->when($request->status, fn ($q, $status) => $q->where('status', $status))->latest()->paginate(15)->withQueryString();

        return view('admin.events', compact('events'));
    }

    public function showEvent(Event $event)
    {
        $event->load(['organizer', 'category', 'ticketTypes', 'registrations.user', 'registrations.digitalTicket']);

        return view('admin.event-show', compact('event'));
    }

    public function editEvent(Event $event)
    {
        $this->authorize('update', $event);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('organizer.event-form', [
            'categories' => $categories,
            'event' => $event,
            'adminMode' => true,
        ]);
    }

    public function updateEvent(StoreEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $data = $request->validated();
        $ticketName = $data['ticket_name'];
        $ticketQuota = $data['ticket_quota'];
        unset($data['ticket_name'], $data['ticket_quota'], $data['poster']);
        $oldPoster = $event->poster_path;
        $newPoster = null;

        if ($request->hasFile('poster')) {
            $newPoster = $request->file('poster')->store('posters', 'public');
            $data['poster_path'] = $newPoster;
        }

        DB::transaction(function () use ($event, $data, $ticketName, $ticketQuota) {
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

        app(ActivityLogger::class)->log('event.updated', $event, 'Event diperbarui oleh admin; status publikasi dipertahankan.');

        return redirect()->route('admin.event.show', $event)->with('success', 'Event berhasil diperbarui.');
    }

    public function approve(Event $event)
    {
        $this->authorize('approve', $event);
        $event->update(['status' => 'published', 'verified_by' => auth()->id(), 'verified_at' => now(), 'rejection_reason' => null]);
        app(ActivityLogger::class)->log('event.approved', $event, 'Event disetujui dan dipublikasikan.');

        return back()->with('success', 'Event dipublikasikan.');
    }

    public function reject(Request $request, Event $event)
    {
        $this->authorize('approve', $event);
        $data = $request->validate(['rejection_reason' => 'required|string|max:1000']);
        $event->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason'], 'verified_by' => auth()->id(), 'verified_at' => now()]);
        app(ActivityLogger::class)->log('event.rejected', $event, 'Event ditolak: '.$data['rejection_reason']);

        return back()->with('success', 'Event ditolak dengan alasan.');
    }

    public function categories()
    {
        $categories = Category::latest()->paginate(15);

        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:80', 'description' => 'nullable|string']);
        Category::create(['name' => $data['name'], 'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)), 'description' => $data['description'] ?? null]);

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function logs()
    {
        $logs = ActivityLog::with('user')->latest('created_at')->paginate(30);

        return view('admin.logs', compact('logs'));
    }
}
