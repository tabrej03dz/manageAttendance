@extends('dashboard.layout.root')
@section('content')


        @if(auth()->user()->hasRole('super_admin') && session('active_office_id'))
            <div class="mb-4 flex items-center justify-between bg-purple-100 border border-purple-300 text-purple-800 px-4 py-3 rounded-lg">
                <div>
                    Viewing as Office:
                    <strong>{{ session('active_office_name') }}</strong>
                </div>

                <form action="{{ route('office.clearSwitch') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        Exit Office View
                    </button>
                </form>
            </div>
        @endif
    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Offices</h1>

            @can('create office')
            <a href="{{ route('office.create') }}"
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create Office
            </a>
            @endcan
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
                        <th class="py-3 px-6 text-left">Under Radius Required</th>
                        <th class="py-3 px-6 text-left">Status</th>
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
                        <td class="py-3 px-6 text-left">{{$office->under_radius_required == '1' ? 'yes' : 'no'}}</td>
                        <td class="py-3 px-6 text-left">
                            @can('office status change')
                            <a href="{{route('office.status', ['office' => $office->id])}}" class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($office->status == 'active')
                                    bg-green-100 text-green-800
                                @else
                                    bg-red-100 text-red-800
                                @endif
                                ">{{ucfirst($office->status)}}
                            </a>
                            @endcan
                        </td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            @can('edit office')
                            <a title="Edit" href="{{route('office.edit', ['office' => $office->id])}}"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </a>
                            @endcan
                            @can('delete office')
                            <a title="Delete" href="{{route('office.delete', ['office' => $office->id])}}"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </a>
                                @endcan

                                @can('show office details')
                            <a title="Details" href="{{route('office.detail', ['office' => $office->id])}}"
                               class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">add</span>
                            </a>
                                @endcan

                                @if(auth()->user()->hasRole('super_admin'))
                                    <form action="{{ route('office.switch', $office->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="View as this office"
                                            class="bg-purple-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-purple-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-50">
                                            <span class="material-icons">login</span>
                                        </button>
                                    </form>
                                @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
