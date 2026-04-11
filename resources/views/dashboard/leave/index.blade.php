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

    <div class="space-y-6">
        <form action="{{ route('leave.index') }}"
              method="GET"
              class="flex flex-wrap items-center gap-4 bg-gray-100 p-4 rounded-lg shadow-md">

            <label for="filter_by_status" class="text-sm font-medium text-gray-700">
                Filter By Status:
            </label>

            <select id="filter_by_status"
                    name="status"
                    class="border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            </select>

            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">
                Filter
            </button>

            <a href="{{ route('leave.index') }}"
               class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out">
                Clear
            </a>
        </form>

        <div class="bg-gray-100 p-4 rounded-lg shadow-md">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Leave List</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Total Records: {{ $leaves->total() }}
                    </p>
                </div>

                <a href="{{ route('leave.create') }}"
                   class="inline-flex items-center justify-center bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                    Request For Leave
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow-md overflow-hidden">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">#</th>
                            <th class="py-3 px-6 text-left">Employee</th>
                            <th class="py-3 px-6 text-left">Start Date</th>
                            <th class="py-3 px-6 text-left">End Date</th>
                            <th class="py-3 px-6 text-left">Days</th>
                            <th class="py-3 px-6 text-left">Leave Type</th>
                            <th class="py-3 px-6 text-left">Reason</th>
                            <th class="py-3 px-6 text-left">Paid/Unpaid</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Response By</th>
                            <th class="py-3 px-6 text-left">Approved As</th>
                            <th class="py-3 px-6 text-left">Action</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse ($leaves as $leave)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6 text-left">
                                    {{ $leaves->firstItem() + $loop->index }}
                                </td>

                                <td class="py-3 px-6 text-left font-medium text-gray-800">
                                    {{ $leave->user->name ?? 'N/A' }}
                                </td>

                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}
                                </td>

                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                </td>

                                <td class="py-3 px-6 text-left">
                                    {{ $leave->day_count ?? 1 }}
                                </td>

                                <td class="py-3 px-6 text-left">
                                    {{ $leave->leave_type }}
                                </td>

                                <td class="py-3 px-6 text-left max-w-[220px]">
                                    <div class="truncate" title="{{ $leave->reason }}">
                                        {{ $leave->reason ?: '-' }}
                                    </div>
                                </td>

                                <td class="py-3 px-6 text-left">
                                    @if((string) $leave->is_paid === '1' || strtolower((string) $leave->is_paid) === 'paid')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Paid
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-6 text-left">
                                    @php
                                        $status = strtolower($leave->status);
                                    @endphp

                                    @if($status === 'approved')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Approved
                                        </span>
                                    @elseif($status === 'rejected')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-6 text-left">
                                    {{ $leave->responsesBy?->name ?? '-' }}
                                </td>

                                <td class="py-3 px-6 text-left">
                                    {{ $leave->approve_as ?? '-' }}
                                </td>

                                <td class="py-3 px-6 text-left">
                                    <div class="flex flex-wrap gap-2">
                                        <a title="Details"
                                           href="{{ route('leave.show', ['leave' => $leave->id]) }}"
                                           class="bg-green-500 text-white font-semibold py-2 px-3 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                            Detail
                                        </a>

                                        @can('approve leave')
                                            @if ($leave->status !== 'approved')
                                                <a title="Approve As Paid"
                                                   href="{{ route('leave.status', ['leave' => $leave->id, 'status' => 'approved', 'type' => 'paid']) }}"
                                                   class="bg-blue-500 text-white font-semibold py-2 px-3 rounded-lg shadow-md hover:bg-blue-600 transition duration-300">
                                                    Approve Paid
                                                </a>

                                                <a title="Approve As Unpaid"
                                                   href="{{ route('leave.status', ['leave' => $leave->id, 'status' => 'approved', 'type' => 'unpaid']) }}"
                                                   class="bg-indigo-500 text-white font-semibold py-2 px-3 rounded-lg shadow-md hover:bg-indigo-600 transition duration-300">
                                                    Approve Unpaid
                                                </a>
                                            @endif
                                        @endcan

                                        @can('reject leave')
                                            @if ($leave->status !== 'rejected')
                                                <a title="Reject"
                                                   href="{{ route('leave.status', ['leave' => $leave->id, 'status' => 'rejected']) }}"
                                                   class="bg-red-500 text-white font-semibold py-2 px-3 rounded-lg shadow-md hover:bg-red-600 transition duration-300">
                                                    Reject
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-6 px-6 text-center text-gray-500">
                                    No leave records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($leaves->hasPages())
                <div class="mt-6">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection