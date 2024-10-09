@extends('dashboard.layout.root')
@section('content')
    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Offices</h1>

            <a href="{{ route('office.create') }}"
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create Office
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Latitude</th>
                        <th class="py-3 px-6 text-left">Longitude</th>
                        <th class="py-3 px-6 text-left">Radius</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                   @foreach($offices as $office)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$office->name}}</td>
                        <td class="py-3 px-6 text-left">{{$office->latitude}}</td>
                        <td class="py-3 px-6 text-left">{{$office->longitude}}</td>
                        <td class="py-3 px-6 text-left">{{$office->radius}}</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            @role('super_admin|admin')
                            <a title="Edit" href="{{route('office.edit', ['office' => $office->id])}}"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </a>
                            <a title="Delete" href="{{route('office.delete', ['office' => $office->id])}}"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
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
