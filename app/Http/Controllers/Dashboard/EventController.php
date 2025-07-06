<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $events = $user->events()
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->paginate(10);

        return view('employees.events', compact('events', 'user'));
    }

    public function show(Event $event)
    {
        $user = Auth::user();
        $participantStatus = $event->participants()->find($user->id)?->pivot?->status;

        return view('employees.event-details', [
            'event' => $event,
            'user' => $user,
            'participantStatus' => $participantStatus
        ]);
    }

    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:confirmed,declined'
        ]);

        $user = Auth::user();
        $event->participants()->updateExistingPivot($user->id, [
            'status' => $request->status
        ]);

        return back()->with('success', 'Event status updated successfully');
    }
}