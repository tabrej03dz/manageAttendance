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
            <h1 class="text-2xl font-bold">Mark attendance</h1>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Image</th>
                    <th class="py-3 px-6 text-left">Office</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($employees as $employee)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$employee->name}}</td>
                        <td class="py-3 px-6 text-left">
                            <img src="https://via.placeholder.com/50" alt="Charlie Brown" class="rounded-full w-12 h-12">
                        </td>
                        <td class="py-3 px-6 text-left">{{$employee->office->name}}</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            @role('super_admin|admin')
                            <a title="Check-in" href="{{ route('attendance.form', ['form_type' => 'check_in', 'user' => $employee->id]) }}"
                               class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">check_circle</span>
                            </a>
                            <a title="Check-out" href="{{ route('attendance.form', ['form_type' => 'check_out', 'user' => $employee->id]) }}"
                               class="bg-yellow-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-yellow-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">logout</span>
                            </a>
                            @endrole
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
