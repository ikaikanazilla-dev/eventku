<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $events = Event::public()->with(['category', 'organizer', 'ticketTypes'])->withCount(['registrations as registered_count' => fn ($q) => $q->where('status', 'registered')])->orderBy('start_at')->limit(6)->get();

        return view('public.home', compact('events'));
    }

    public function events(Request $request)
    {
        $query = Event::public()->with(['category', 'organizer', 'ticketTypes'])->withCount(['registrations as registered_count' => fn ($q) => $q->where('status', 'registered')]);
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->q.'%')->orWhere('location', 'like', '%'.$request->q.'%'));
        } if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        } if ($request->filled('date')) {
            $query->whereDate('start_at', $request->date);
        } $events = $query->orderBy('start_at')->paginate(9)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('public.events', compact('events', 'categories'));
    }

    public function show(string $slug)
    {
        $event = Event::public()->with(['category', 'organizer', 'ticketTypes'])->where('slug', $slug)->firstOrFail();
        $registration = auth()->check() && auth()->user()->isRole('participant') ? $event->registrations()->with('digitalTicket')->where('user_id', auth()->id())->where('status', 'registered')->first() : null;

        return view('public.event', compact('event','registration'));
    }
}
