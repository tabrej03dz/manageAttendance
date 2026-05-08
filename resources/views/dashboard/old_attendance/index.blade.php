@extends('dashboard.layout.root')

@section('content')

<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-6">

        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-xl p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Old Attendance Records</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Records older than {{ $cutoffDate->format('d M Y') }}
                    </p>
                    <p class="text-sm text-red-600 mt-1">
                        Total Old Records: {{ $totalOldRecords }}
                    </p>
                </div>

                @role('super_admin|admin|owner')
                <form action="{{ route('old-attendance.destroy-old-records') }}" method="POST"
                      onsubmit="return confirm('Are you sure? All records older than 1 year will be deleted permanently.')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700">
                        Delete All Old Records
                    </button>
                </form>
                @endrole
            </div>
        </div>

        <form action="{{ route('old-attendance.index') }}" method="GET">
            <div class="bg-white p-4 rounded-lg shadow-md mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">

                @role('super_admin|admin|owner')
                <div>
                    <label class="text-sm font-medium text-gray-700">Employee</label>
                    <select name="employee" class="border-gray-300 rounded-md shadow-sm p-2 w-full">
                        <option value="">All Employees</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('employee') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endrole

                <div>
                    <label class="text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                           class="border-gray-300 rounded-md shadow-sm p-2 w-full">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                           class="border-gray-300 rounded-md shadow-sm p-2 w-full">
                </div>

                <div class="flex items-end gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Filter
                    </button>

                    <a href="{{ route('old-attendance.index') }}"
                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        Clear
                    </a>
                </div>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Check In</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Check Out</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Duration</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Day Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Late</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Action</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $record->created_at?->format('d M Y') }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $record->user?->name ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('h:i A') : 'N/A' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : 'N/A' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $record->duration ? App\Http\Controllers\HomeController::getTime($record->duration) : 'N/A' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $record->day_type ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-red-600">
                                {{ $record->late ? App\Http\Controllers\HomeController::getTime($record->late) : 'N/A' }}
                            </td>

                            <td class="px-4 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('old-attendance.show', $record->id) }}"
                                       class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        View
                                    </a>

                                    @role('super_admin|admin|owner')
                                    <form action="{{ route('old-attendance.destroy', $record->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this old record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No old attendance records found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>

@endsection