@extends('dashboard.layout.root')
@section('content')

    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
        <h2 class="text-2xl font-bold mb-4">Edit Department</h2>

        <form action="{{ route('departments.update', $department->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Department Name</label>
                <input type="text" name="name" value="{{ old('name', $department->name) }}"
                       class="w-full border p-2 rounded-lg focus:ring-2 focus:ring-red-400">
                @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('departments.index') }}"
                   class="bg-gray-300 px-4 py-2 rounded-lg">Cancel</a>

                <button type="submit"
                        class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg shadow-md hover:shadow-lg">
                    Update
                </button>
            </div>
        </form>
    </div>

@endsection
