@extends('dashboard.layout.root')

@section('content')

    <div class="pt-10 pb-20">
        <!-- Card Container with border -->
        <div class="w-full max-w-screen-lg mx-auto p-6 bg-white rounded-lg shadow-lg border border-red-500">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-semibold text-gray-900">Half Day</h2>
            </div>

            <form action="{{ route('half-day.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <!-- Date Field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1" for="date">Date</label>
                    <input type="date" id="date" name="date" value="{{old('date')}}" class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" />
                    @error('date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason Field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1" for="reason">Reason for applying</label>
                    <textarea id="reason" name="reason" class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" rows="4" placeholder="Enter your reason here">{{old('reason')}}</textarea>
                    @error('reason')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1" for="image">Upload Image</label>
                    <input type="file" id="image" name="image" accept="image" class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('image.*')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-between">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 hover:bg-red-500">Send for approval</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr (commented fields not needed here)
        flatpickr("#date", {
            dateFormat: "Y-m-d"
        });
    </script>
@endsection
