@extends('dashboard.layout.root')
@section('content')
    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">{{$role->name.'\'s permissions'}}</h1>


        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($permissions as $permission)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$permission->name}}</td>

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
                            @can('delete role\'s permission')
                            <a title="remove" href="{{ route('role.permissionRemove', ['permission' => $permission->id, 'role' => $role->id]) }}"
                               class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span> <!-- Correct icon name -->
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
