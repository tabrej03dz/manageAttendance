@extends('dashboard.layout.root')

@section('content')

<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-6">

        <div class="bg-white rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Old Attendance Detail</h2>
                    <p class="text-sm text-gray-500">
                        {{ $record->created_at?->format('d M Y') }}
                    </p>
                </div>

                <a href="{{ route('old-attendance.index') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Back
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">Employee Info</h3>
                    <p><b>Name:</b> {{ $record->user?->name ?? 'N/A' }}</p>
                    <p><b>User ID:</b> {{ $record->user_id }}</p>
                    <p><b>Day Type:</b> {{ $record->day_type ?? 'N/A' }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">Time Info</h3>
                    <p><b>Check In:</b> {{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('d M Y h:i A') : 'N/A' }}</p>
                    <p><b>Check Out:</b> {{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('d M Y h:i A') : 'N/A' }}</p>
                    <p><b>Duration:</b> {{ $record->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : 'N/A' }}</p>
                    <p><b>Late:</b> {{ $record->late ? App\Http\Controllers\HomeController::getTime($record->late) : 'N/A' }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">Check In Details</h3>
                    <p><b>Distance:</b> {{ $record->check_in_distance ? round($record->check_in_distance) . ' m' : 'N/A' }}</p>
                    <p><b>Latitude:</b> {{ $record->check_in_latitude ?? 'N/A' }}</p>
                    <p><b>Longitude:</b> {{ $record->check_in_longitude ?? 'N/A' }}</p>
                    <p><b>By:</b> {{ $record->checkInBy?->name ?? 'N/A' }}</p>
                    <p><b>Note:</b> {{ $record->check_in_note ?? 'N/A' }}</p>
                    <p><b>Note Status:</b> {{ $record->check_in_note_status ?? 'N/A' }}</p>

                    @if($record->check_in_image)
                        <a href="{{ asset('storage/' . $record->check_in_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $record->check_in_image) }}"
                                 class="w-24 h-24 rounded-lg mt-3 object-cover">
                        </a>
                    @endif
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">Check Out Details</h3>
                    <p><b>Distance:</b> {{ $record->check_out_distance ? round($record->check_out_distance) . ' m' : 'N/A' }}</p>
                    <p><b>Latitude:</b> {{ $record->check_out_latitude ?? 'N/A' }}</p>
                    <p><b>Longitude:</b> {{ $record->check_out_longitude ?? 'N/A' }}</p>
                    <p><b>By:</b> {{ $record->checkOutBy?->name ?? 'N/A' }}</p>
                    <p><b>Note:</b> {{ $record->check_out_note ?? 'N/A' }}</p>
                    <p><b>Note Status:</b> {{ $record->check_out_note_status ?? 'N/A' }}</p>

                    @if($record->check_out_image)
                        <a href="{{ asset('storage/' . $record->check_out_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $record->check_out_image) }}"
                                 class="w-24 h-24 rounded-lg mt-3 object-cover">
                        </a>
                    @endif
                </div>
            </div>

            @role('super_admin|admin|owner')
            <div class="mt-6">
                <form action="{{ route('old-attendance.destroy', $record->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure? This old attendance record will be deleted permanently.')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700">
                        Delete This Record
                    </button>
                </form>
            </div>
            @endrole
        </div>
    </div>
</div>

@endsection