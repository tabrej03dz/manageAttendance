@extends('dashboard.layout.root')
@section('content')

    <style>
        #more-options {
            max-height: 0;
            overflow: hidden;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Employees</h1>

            <a href="{{route('visit.create')}}"
               class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Employee</th>
                    <th class="py-3 px-6 text-left">Photo</th>
                    <th class="py-3 px-6 text-left">Address</th>
                    <th class="py-3 px-6 text-left">Expense</th>
                    <th class="py-3 px-6 text-left">Expense Attachment</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Expense Paid</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($visits as $visit)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$visit->user->name}}</td>
                        <td class="py-3 px-6 text-left">
                            <img src="{{$visit->photo ? asset('storage/'. $visit->photo) : 'https://via.placeholder.com/50'}}" alt="{{$visit->user->name}}" class="rounded-full w-12 h-12">
                        </td>
                        @php
                            if ($visit->latitude && $visit->longitude){
                                $visit_latitude = App\Http\Controllers\HomeController::latitudeInDMS($visit->latitude);
                                $visit_longitude = App\Http\Controllers\HomeController::longitudeInDMS($visit->longitude);
                            }

                        @endphp
                        <td class="py-3 px-6 text-left">
                            @if($visit->latitude && $visit->longitude)
                            <a target="_blank" href="{{'https://www.google.com/maps/place/'.$visit_latitude.'+'.$visit_longitude.'/@'.$visit->latitude.','.$visit->longitude.',17z/data=!4m4!3m3!8m2!3d26.5004167!4d80.2878611?authuser=0&entry=ttu&g_ep=EgoyMDI0MTAyMC4xIKXMDSoASAFQAw%3D%3D'}}">
                            {{$visit->address}}
                            </a>
                            @else
                                {{$visit->address}}
                            @endif
                        </td>
                        <td class="py-3 px-6 text-left">{{$visit->expense}}</td>
                        <td class="py-3 px-6 text-left">
                            <img src="{{asset('storage/'. $visit->expense_attachment)}}" alt="">
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{$visit->description}}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{$visit->status}}
                        </td>
                        <td class="py-3 px-6 text-left">
                            @if($visit->expense_paid == '1')
                                <a title="Paid" href="#"
                                   class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                    <span class="material-icons">check</span>
                                </a>

                            @else
                                @role('super_admin|admin')
                                <a title="Mark as Paid" href="{{ route('visit.paid', ['visit' => $visit->id]) }}"
                                   class="flex items-center justify-center bg-green-500 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50 w-full md:w-auto">
                                    Mark as Paid
                                </a>
                                @else
                                    <a title="Paid" href="#"
                                       class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                        <span class="material-icons">close</span>
                                    </a>

                                    @endrole

                            @endif
                        </td>
                        <td class="py-3 px-6 text-left">
                            @role('super_admin|admin')
                            <div class="flex flex-wrap space-x-3 space-y-3 md:space-y-0 md:flex-nowrap">
                                <!-- Approve Button -->
                                @if($visit->status != 'approved')
                                <a title="Approve" href="{{ route('visit.status', ['visit' => $visit->id, 'status' => 'approved']) }}"
                                   class="flex items-center justify-center bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 w-full md:w-auto">
                                    Approve
                                </a>
                                @endif

                                <!-- Reject Button -->
                                @if($visit->status != 'rejected')
                                <button title="Reject" type="submit"
                                        class="flex items-center justify-center bg-red-500 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50 w-full md:w-auto">
                                    Reject
                                </button>
                                @endif
                            </div>
                            @endrole
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection
