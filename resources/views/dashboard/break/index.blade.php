@extends('dashboard.layout.root')
@section('content')

<style>
    #more-options {
        max-height: 0;
        overflow: hidden;
    }

    .pagination {
        margin-bottom: 0;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="bg-gray-100 p-4 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4 space-x-4">
        <h1 class="text-2xl font-bold">Breaks</h1>

        <div class="flex items-center space-x-2">
            <form action="{{ route('break.index') }}" method="get" class="flex items-center space-x-2">
                <input type="date" name="date" value="{{ $date }}" class="form-control">
                <input type="submit" value="Filter" class="btn btn-success">
            </form>

            <a href="{{ route('break.index') }}" class="btn btn-secondary">Clear</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left border border-gray-300">#</th>
                    <th class="py-3 px-6 text-left border border-gray-300">Name</th>
                    <th class="py-3 px-6 text-left border border-gray-300">Break Details</th>
                </tr>
            </thead>

            <tbody class="text-gray-600 text-sm font-light">
                @forelse($users as $user)
                    <tr class="border-b border-gray-300 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left border border-gray-300">
                            {{ ($users->firstItem() ?? 1) + $loop->index }}
                        </td>

                        <td class="py-3 px-6 text-left border border-gray-300">
                            {{ $user->name }}
                        </td>

                        <td class="border border-gray-300">
                            <table class="w-full border border-gray-300 text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-2 py-1 border border-gray-300">Start</th>
                                        <th class="px-2 py-1 border border-gray-300">End</th>
                                        <th class="px-2 py-1 border border-gray-300">Start Distance</th>
                                        <th class="px-2 py-1 border border-gray-300">Start Image</th>
                                        <th class="px-2 py-1 border border-gray-300">End Distance</th>
                                        <th class="px-2 py-1 border border-gray-300">End Image</th>
                                        <th class="px-2 py-1 border border-gray-300">Duration (mins)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if($user->breaks && $user->breaks->count() > 0)
                                        @foreach($user->breaks as $break)
                                            @php
                                                $start_time = $break->start_time ? \Carbon\Carbon::parse($break->start_time) : null;
                                                $end_time = $break->end_time ? \Carbon\Carbon::parse($break->end_time) : null;
                                            @endphp

                                            <tr class="hover:bg-gray-50">
                                                <td class="px-2 py-1 border border-gray-300">
                                                    {{ $break->start_time ?? 'N/A' }}
                                                </td>

                                                <td class="px-2 py-1 border border-gray-300">
                                                    {{ $break->end_time ?? 'N/A' }}
                                                </td>

                                                <td class="px-2 py-1 border border-gray-300">
                                                    {{ $break->start_distance !== null ? round($break->start_distance) : 'N/A' }}
                                                </td>

                                                <td class="px-2 py-1 border border-gray-300">
                                                    @if($break->start_image)
                                                        <a href="{{ asset('storage/' . $break->start_image) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $break->start_image) }}"
                                                                 alt="Start Image"
                                                                 class="w-12 h-12 object-cover border border-gray-200 rounded">
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>

                                                <td class="px-2 py-1 border border-gray-300">
                                                    {{ $break->end_distance !== null ? round($break->end_distance) : 'N/A' }}
                                                </td>

                                                <td class="px-2 py-1 border border-gray-300">
                                                    @if($break->end_image)
                                                        <a href="{{ asset('storage/' . $break->end_image) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $break->end_image) }}"
                                                                 alt="End Image"
                                                                 class="w-12 h-12 object-cover border border-gray-200 rounded">
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>

                                                <td class="px-2 py-1 border border-gray-300">
                                                    {{ $start_time && $end_time ? $start_time->diffInMinutes($end_time) : 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-500 py-2">
                                                No breaks recorded for selected date
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500 py-4 border border-gray-300">
                            No employees found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} employees
        </div>

        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>

@endsection