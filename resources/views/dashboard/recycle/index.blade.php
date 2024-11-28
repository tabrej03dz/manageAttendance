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

{{--            <a href="{{route('employee.create')}}"--}}
{{--               class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">--}}
{{--                Create--}}
{{--            </a>--}}
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Email</th>
                    <th class="py-3 px-6 text-left">Phone</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($users as $user)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$user->name}}</td>
                        <td class="py-3 px-6 text-left">{{$user->email}}</td>
                        <td class="py-3 px-6 text-left">{{$user->phone}}</td>

                        <td class="py-3 px-6 text-left flex space-x-2">


                           @can('permanent delete employee')
                            <!-- Hard Delete Button -->
                            <a title="Hard Delete" href="{{ route('recycle.user.delete', ['user' => $user->id]) }}"
                               class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50"
                               onclick="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')">
                                <span class="material-icons">delete_forever</span>
                            </a>
                            @endcan
                            @can('restore employee')
                            <a title="Restore" href="{{ route('recycle.user.restore', ['user' => $user->id]) }}"
                               class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">restore</span>
                            </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection
