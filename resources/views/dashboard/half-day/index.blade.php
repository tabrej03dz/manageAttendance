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


    <form action="{{ route('half-day.index') }}" class="flex flex-wrap items-center gap-4 bg-gray-100 p-4 rounded-lg shadow-md">
        <!-- Filter By Status -->
        <label for="filter_by_status" class="text-sm font-medium text-gray-700">Filter By Status:</label>
        <select id="filter_by_status" name="status" onchange="this.form.submit()"
                class="border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Select Status</option>
            <option value="pending" {{$status == 'pending' ? 'selected' : ''}}>Pending</option>
            <option value="rejected" {{$status == 'rejected' ? 'selected' : ''}}>Rejected</option>
            <option value="approved" {{$status == 'approved' ? 'selected' : ''}}>Approved</option>
        </select>

        <label for="month" class="text-sm font-medium text-gray-700">Filter By Month:</label>
        <input id="month" name="month" onchange="this.form.submit()" type="month" value="{{$month}}"
                class="border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>

        <!-- Filter Button -->
{{--        <button--}}
{{--            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">--}}
{{--            Filter--}}
{{--        </button>--}}

        <!-- Clear Button -->
        <a href="{{ route('half-day.index') }}"
           class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out">
            Clear
        </a>
    </form>


    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Leave List</h1>

            <a href="{{route('half-day.create')}}"
               class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Request For Half Day
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Employee</th>
                    <th class="py-3 px-6 text-left">Date</th>
                    <th class="py-3 px-6 text-left">Reason</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Respond By</th>

                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach ($halfDays as $halfDay)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                        <td class="py-3 px-6 text-left">{{$halfDay->user->name }}</td>
                        <td class="py-3 px-6 text-left">{{ $halfDay->date }}</td>
                        <td class="py-3 px-6 text-left">{{ $halfDay->reason }}</td>
                        <td class="py-3 px-6 text-left">
                            @if($halfDay->image)
                                <a href="{{asset('storage/'. $halfDay->image )}}" target="_blank"><img src="{{asset('storage/'. $halfDay->image )}}" alt="" style="width: 100px;"></a>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-left">
                            <form action="{{route('half-day.status', $halfDay->id)}}" method="POST">
                                @csrf
                                <select name="status" id="status" onchange="this.form.submit()">
                                    <option value="pending" {{$halfDay->status == 'pending' ? 'selected' : ''}}>Pending</option>
                                    <option value="rejected" {{$halfDay->status == 'rejected' ? 'selected' : ''}}>Rejected</option>
                                    <option value="approved" {{$halfDay->status == 'approved' ? 'selected' : ''}}>Approved</option>
                                </select>
                            </form>
                        </td>
                        <td class="py-3 px-6 text-left">{{ $halfDay->respondBy?->name }}</td>

                    </tr>
                @endforeach
                <!-- Additional rows can go here -->
                </tbody>
            </table>
        </div>
    </div>
@endsection
