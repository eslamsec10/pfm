@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
@endphp
@section('title', ui_change('meeting_room_booking'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                {{-- <img width="60" src="{{ asset('assets/back-end/img/' . $route . '.jpg') }}" alt=""> --}}
                {{ ui_change('meeting_room_booking') }}
            </h2>
        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col-12" id="calendar"></div>
        </div>
    </div>



    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="bookingForm" method="POST" action="{{ route('meeting_room_booking.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">
                            {{ ui_change('create_meeting_room_booking', 'room_reservation') }}</h5>
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="start_at" id="start_at">
                        <input type="hidden" name="end_at" id="end_at">

                        <div class="mb-3">
                            <label for="meeting_room_id"
                                class="form-label">{{ ui_change('meeting_room', 'room_reservation') }}</label>
                            <select class="form-control" name="meeting_room_id" id="meeting_room_id" required>
                                <option value="" selected disabled>{{ ui_change('select', 'room_reservation') }}
                                </option>
                                @foreach ($meetingRooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->capacity }}
                                        {{ ui_change('people', 'room_reservation') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tenant_id" class="form-label">{{ ui_change('tenant', 'room_reservation') }}</label>
                            <select class="form-control" name="tenant_id" id="tenant_id" required>
                                <option value="" selected disabled>{{ ui_change('select', 'room_reservation') }}
                                </option>
                                @foreach ($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{ ui_change('price', 'room_reservation') }}</label>
                            <input type="number" step="0.001" class="form-control" name="price" id="price">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ ui_change('notes', 'room_reservation') }}</label>
                            <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ ui_change('status', 'room_reservation') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="pending">{{ ui_change('pending', 'room_reservation') }}</option>
                                <option value="confirmed">{{ ui_change('confirmed', 'room_reservation') }}</option>
                                <option value="cancelled">{{ ui_change('cancelled', 'room_reservation') }}</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit"
                            class="btn btn-primary">{{ ui_change('submit', 'room_reservation') }}</button>
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ ui_change('close', 'room_reservation') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        headerToolbar: {      
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: @json($events),

        selectable: true,
        select: function(info) {
            document.getElementById('start_at').value = info.startStr;
            document.getElementById('end_at').value = info.endStr;

            var bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            bookingModal.show();
        }
    });
    calendar.render();
});

    </script>
@endpush
