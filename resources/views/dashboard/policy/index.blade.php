@extends('dashboard.layout.root')
@section('content')

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Policies</h1>

            <a href="{{route('policy.create')}}"
               class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Make Policy
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Title</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    @role('super_admin')
                    <th class="py-3 px-6 text-left">Description</th>
                    @endrole
                    @role('super_admin|admin')
                    <th class="py-3 px-6 text-left">Action</th>
                    @endrole
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($policies as $policy)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$policy->title}}</td>
                        <td class="py-3 px-6 text-left">{!! $policy->description !!}</td>
                        @role('super_admin')
                        <td class="py-3 px-6 text-left">{{$policy->office->name}}</td>
                        @endrole
                        @role('super_admin|admin')
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <a title="Edit" href="{{route('policy.edit', ['policy' => $policy->id])}}"
                               class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </a>
                            <form action="{{route('policy.delete', ['policy' => $policy->id])}}" method="post">
                                @csrf
                                <button title="Delete" type="submit"
                                        class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                        </td>
                        @endrole
                    </tr>
                @endforeach
                <!-- Additional rows can go here -->
                </tbody>
            </table>
        </div>
    </div>


@endsection
