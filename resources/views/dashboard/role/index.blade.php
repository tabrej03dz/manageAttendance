@extends('dashboard.layout.root')
@section('content')
    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Offices</h1>

            <a href="{{ route('role.create') }}"
               class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create Role
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Created By</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($roles as $role)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        @if(auth()->user()->hasRole('super_admin'))
                            <td class="py-3 px-6 text-left">{{$role->name}}</td>
                        @else
                            <td class="py-3 px-6 text-left">{{explode('__', $role->name)[1]}}</td>
                        @endif
                        <td class="py-3 px-6 text-left">{{$role->createdBy?->name}}</td>

{{--                        <td class="py-3 px-6 text-left">--}}
{{--                            <a href="{{route('office.status', ['office' => $office->id])}}" class="px-2 py-1 rounded-full text-xs font-semibold--}}
{{--                                @if($office->status == 'active')--}}
{{--                                    bg-green-100 text-green-800--}}
{{--                                @else--}}
{{--                                    bg-red-100 text-red-800--}}
{{--                                @endif--}}
{{--                                ">{{ucfirst($office->status)}}--}}
{{--                            </a>--}}
{{--                        </td>--}}
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <a title="delete" href="{{ route('role.delete', ['role' => $role->id]) }}"
                               class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span> <!-- Correct icon name -->
                            </a>

                            <a title="permissions" href="{{ route('role.permission', ['role' => $role->id]) }}"
                               class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">manage_accounts</span> <!-- Example of a valid Material Icon -->
                            </a>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
