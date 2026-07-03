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

        <form method="GET" action="{{ route('office.index') }}" class="bg-white p-4 rounded-lg shadow mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Search</label>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Office name, latitude, longitude, radius"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400">
                        <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Under Radius Required</label>
                    <select name="under_radius_required"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-400">
                        <option value="">All</option>
                        <option value="1" {{ request('under_radius_required') === '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ request('under_radius_required') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="bg-red-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-red-700">
                        Filter
                    </button>

                    <a href="{{ route('office.index') }}"
                    class="bg-gray-500 text-white font-semibold px-5 py-2 rounded-lg hover:bg-gray-600">
                        Reset
                    </a>
                </div>

            </div>
        </form>

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
                        <td class="py-3 px-6 text-left">
                            {{ $offices->firstItem() + $loop->index }}
                        </td>
                        <td class="py-3 px-6 text-left">{{$office->name}}</td>
                        <td class="py-3 px-6 text-left">{{$office->latitude}}</td>
                        <td class="py-3 px-6 text-left">{{$office->longitude}}</td>
                        <td class="py-3 px-6 text-left">{{$office->radius}}</td>
                        <td class="py-3 px-6 text-left">{{$office->under_radius_required == '1' ? 'yes' : 'no'}}</td>
                        <td class="py-3 px-6 text-left">
                            @can('office status change')
                                <a href="{{ route('office.status', ['office' => $office->id]) }}"
                                onclick="return confirm('Are you sure you want to change this office status?')"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold shadow-md transition duration-300
                                        @if($office->status == 'active')
                                            bg-green-500 text-white hover:bg-green-600
                                        @else
                                            bg-red-500 text-white hover:bg-red-600
                                        @endif">

                                    @if($office->status == 'active')
                                        <span class="material-icons text-sm">check_circle</span>
                                        Active
                                    @else
                                        <span class="material-icons text-sm">cancel</span>
                                        Inactive
                                    @endif
                                </a>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($office->status == 'active')
                                        bg-green-100 text-green-800
                                    @else
                                        bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($office->status) }}
                                </span>
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
            <div class="mt-4">
                {{ $offices->links() }}
            </div>
        </div>
    </div>
@endsection
