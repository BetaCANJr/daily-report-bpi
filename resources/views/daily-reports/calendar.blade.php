@extends('layouts.user-nav')

@section('title', 'Calendar - Daily Report LPK BPI')
@section('page-title', 'Calendar Laporan')
@section('page-subtitle', 'Lihat laporan dalam bentuk kalender')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    #calendar {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .fc-event {
        cursor: pointer;
        border: none;
        font-size: 0.875rem;
        padding: 2px 4px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: @json($calendarEvents),
        eventClick: function(info) {
            window.location.href = info.event.extendedProps.url;
        },
        eventDisplay: 'block',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });
    calendar.render();
});
</script>
@endpush