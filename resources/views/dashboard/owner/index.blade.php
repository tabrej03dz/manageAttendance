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
            <h1 class="text-2xl font-bold">Owners</h1>

            @can('create owners')
            <a href="{{route('owner.create')}}"
               class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create Owner
            </a>
            @endcan
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Email</th>
                    <th class="py-3 px-6 text-left">Phone</th>
                    <th class="py-3 px-6 text-left">Image</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($owners as $owner)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$owner->name}}</td>
                        <td class="py-3 px-6 text-left">{{$owner->email}}</td>
                        <td class="py-3 px-6 text-left">{{$owner->phone}}</td>
                        <td class="py-3 px-6 text-left">
                            <img src="{{$owner->photo ? asset('storage/'. $owner->photo) : 'https://via.placeholder.com/50'}}" alt="{{$owner->name}}" class="rounded-full w-12 h-12">
                        </td>
                        <td class="py-3 px-6 text-left">
                            <a href="{{route('owner.status', ['owner' => $owner->id])}}" class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($owner->status == '1')
                                    bg-green-100 text-green-800">Active
                                @else
                                    bg-red-100 text-red-800">Inactive
                                @endif

                            </a>
                        </td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            @role('super_admin|admin')
                            <a title="Edit" href="{{ route('owner.edit', ['owner' => $owner->id]) }}"
                               class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </a>
                            <form action="{{ route('owner.delete', ['owner' => $owner->id]) }}" method="post">
                                @csrf
                                <button title="Delete" type="submit"
                                        class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                            <a title="Plans" href="{{ route('owner.plan', ['owner' => $owner->id]) }}"
                               class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">details</span>
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
