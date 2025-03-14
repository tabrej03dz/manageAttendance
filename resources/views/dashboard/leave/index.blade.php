@extends('dashboard.layout.root')

@section('content')
    <style>
        #more-options {
            max-height: 0;
            overflow: hidden;
        }

        .perspective-500 {
            perspective: 500px;
        }
    </style>


    <form action="{{ route('leave.index') }}" class="flex flex-wrap items-center gap-4 bg-gray-100 p-4 rounded-lg shadow-md">
        <!-- Filter By Status -->
        <label for="filter_by_status" class="text-sm font-medium text-gray-700">Filter By Status:</label>
        <select id="filter_by_status" name="status"
                class="border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Select Status</option>
            <option value="pending">Pending</option>
            <option value="rejected">Rejected</option>
            <option value="approved">Approved</option>
        </select>

        <!-- Filter Button -->
        <button
            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">
            Filter
        </button>

        <!-- Clear Button -->
        <a href="{{ route('leave.index') }}"
           class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out">
            Clear
        </a>
    </form>


    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Leave List</h1>

                        <a href="{{route('leave.create')}}"
                            class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                            Request For Leave
                        </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Employee</th>
                        <th class="py-3 px-6 text-left">Start Date</th>
                        <th class="py-3 px-6 text-left">End Date</th>
                        <th class="py-3 px-6 text-left">Leave Type</th>
{{--                        <th class="py-3 px-6 text-left">Reason</th>--}}
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Response By</th>
                        <th class="py-3 px-6 text-left">Approved As</th>

                            <th class="py-3 px-6 text-left">Action</th>

                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($leaves as $leave)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                            <td class="py-3 px-6 text-left">{{ $leave->user->name }}</td>
                            <td class="py-3 px-6 text-left">{{ $leave->start_date }}</td>
                            <td class="py-3 px-6 text-left">{{ $leave->end_date }}</td>
                            <td class="py-3 px-6 text-left">{{ $leave->leave_type }}</td>
{{--                            <td class="py-3 px-6 text-left">{{ $leave->reason }}</td>--}}
                            <td class="py-3 px-6 text-left">{{ $leave->status }}</td>
                            <td class="py-3 px-6 text-left">{{ $leave->responsesBy?->name }}</td>
                            <td class="py-3 px-6 text-left">{{ $leave->approve_as }}</td>

                                <td class="py-3 px-6 text-left flex space-x-2">
                                    @can('approve leave')
{{--                                    @if ($leave->status != 'approved')--}}
{{--                                        <a title="Approve"--}}
{{--                                            href="{{ route('leave.status', ['leave' => $leave->id, 'status' => 'approved', 'type' => 'paid']) }}"--}}
{{--                                            class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">--}}
{{--                                            Approve As Paid--}}
{{--                                        </a>--}}
{{--                                            <a title="Approve"--}}
{{--                                               href="{{ route('leave.status', ['leave' => $leave->id, 'status' => 'approved', 'type' => 'unpaid']) }}"--}}
{{--                                               class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">--}}
{{--                                                Approve As UnPaid--}}
{{--                                            </a>--}}
{{--                                    @endif--}}
                                    @endcan
                                    @can('reject leave')
{{--                                    @if ($leave->status != 'rejected')--}}
{{--                                        <a title="Reject"--}}
{{--                                            href="{{ route('leave.status', ['leave' => $leave->id, 'status' => 'rejected']) }}"--}}
{{--                                            class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">--}}
{{--                                            <span class="material-icons">cancel</span>--}}
{{--                                        </a>--}}
{{--                                    @endif--}}
                                        @endcan

                                            <a title="Details"
                                                href="{{ route('leave.show', ['leave' => $leave->id]) }}"
                                                class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                                Detail
                                            </a>

                                </td>

                        </tr>
                    @endforeach
                    <!-- Additional rows can go here -->
                </tbody>
            </table>
        </div>
    </div>
@endsection
