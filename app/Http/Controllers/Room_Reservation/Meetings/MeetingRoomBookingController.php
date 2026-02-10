<?php

namespace App\Http\Controllers\Room_Reservation\Meetings;

use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use App\Models\MeetingRoomBooking;
use App\Http\Controllers\Controller;
use App\Models\Tenant;

class MeetingRoomBookingController extends Controller
{
    public function index()
    {
        $bookings = MeetingRoomBooking::with('meetingRoom')->get();
        $meetingRooms = MeetingRoom::get();
        $tenants = Tenant::get();
        $events = $bookings->map(function ($b) {
            return [
                'title' => $b->meetingRoom->name,
                'start' => $b->start_at,
                'end'   => $b->end_at,
                'color' => $b->status == 'confirmed' ? 'green' : 'orange',
            ];
        });
        $data = [
            'bookings' => $bookings,
            'meetingRooms' => $meetingRooms,
            'events' => $events,
            'tenants' => $tenants,
        ];

        return view('admin-views.room_reservation.meetings.bookings.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'tenant_id'       => 'required|exists:users,id',
            'start_at'        => 'required|date',
            'end_at'          => 'required|date|after:start_at',
        ]);

        // Prevent double booking
        $conflict = MeetingRoomBooking::where('meeting_room_id', $request->meeting_room_id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_at', [$request->start_at, $request->end_at])
                    ->orWhereBetween('end_at', [$request->start_at, $request->end_at]);
            })->exists();

        if ($conflict) {
            return back()->withErrors('This room is already booked for this time.');
        }

        $price = MeetingRoom::find($request->meeting_room_id)->rent_amount; // basic price

        MeetingRoomBooking::create([
            'meeting_room_id' => $request->meeting_room_id,
            'tenant_id'       => $request->tenant_id,
            'start_at'        => $request->start_at,
            'end_at'          => $request->end_at,
            'status'          => 'confirmed',
            'price'           => $price,
            'created_by'      => auth()->id(),
        ]);

        return back()->withSuccess('Booking confirmed!');
    }

    public function edit(MeetingRoomBooking $booking)
    {
        $rooms = MeetingRoom::all();
        $tenants = Tenant::all();
        return view('admin-views.room_reservation.meetings.bookings.edit', compact('booking', 'rooms', 'tenants'));
    }

    public function update(Request $request, MeetingRoomBooking $booking)
    {
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'tenant_id'       => 'required|exists:users,id',
            'start_at'        => 'required|date',
            'end_at'          => 'required|date|after:start_at',
        ]);

        // Check for conflict except current booking
        $conflict = MeetingRoomBooking::where('meeting_room_id', $request->meeting_room_id)
            ->where('id', '!=', $booking->id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_at', [$request->start_at, $request->end_at])
                    ->orWhereBetween('end_at', [$request->start_at, $request->end_at]);
            })->exists();

        if ($conflict) {
            return back()->withErrors('This room is already booked for this time.');
        }

        $booking->update([
            'meeting_room_id' => $request->meeting_room_id,
            'tenant_id'       => $request->tenant_id,
            'start_at'        => $request->start_at,
            'end_at'          => $request->end_at,
            'status'          => $request->status ?? $booking->status,
        ]);

        return back()->withSuccess('Booking updated!');
    }
}
