@extends('dashboard.layout.root')
@section('content')

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Departments</h1>

            <a href="{{ route('departments.create') }}"
               class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                Create Department
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Department Name</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($departments as $department)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                        <td class="py-3 px-6 text-left">{{ $department->name }}</td>
                        <td class="py-3 px-6 text-left flex space-x-2">

                            <a title="Edit" href="{{ route('departments.edit', $department->id) }}"
                               class="bg-blue-500 text-white p-2 rounded-lg shadow-md hover:bg-blue-600 transition">
                                <span class="material-icons">edit</span>
                            </a>

                            <form action="{{ route('departments.destroy', $department->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button title="Delete" type="submit"
                                        class="bg-red-500 text-white p-2 rounded-lg shadow-md hover:bg-red-600 transition">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
