@extends('dashboard.layout.root')
@section('content')
    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Notes</h1>

                <a href="{{ route('note.create') }}"
                   class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                    Create Note
                </a>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Title</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($notes as $note)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$note->title}}</td>
                        <td class="py-3 px-6 text-left">{{$note->description}}</td>
                        <td class="py-3 px-6 text-left">
                            <a href="{{route('note.status', ['note' => $note->id])}}" class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($note->status == '1')
                                    bg-green-100 text-green-800">active
                                @else
                                    bg-red-100 text-red-800">inactive
                                @endif
                            </a>
                        </td>
                        <td class="py-3 px-6 text-left flex space-x-2">
{{--                            @can('edit office')--}}
{{--                                <a title="Edit" href="{{route('office.edit', ['office' => $office->id])}}"--}}
{{--                                   class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">--}}
{{--                                    <span class="material-icons">edit</span>--}}
{{--                                </a>--}}
{{--                            @endcan--}}
                            @can('delete office')
                                <a title="Delete" href="{{route('note.delete', ['note' => $note->id])}}"
                                   class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                    <span class="material-icons">delete</span>
                                </a>
                            @endcan

{{--                            @can('show office details')--}}
{{--                                <a title="Details" href="{{route('office.detail', ['office' => $office->id])}}"--}}
{{--                                   class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">--}}
{{--                                    <span class="material-icons">add</span>--}}
{{--                                </a>--}}
{{--                            @endcan--}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
