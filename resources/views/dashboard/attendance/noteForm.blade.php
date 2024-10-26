@extends('dashboard.layout.root')

@section('content')
    <!-- Add Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .punch-circle {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.3);
            position: relative;
            overflow: hidden;
        }

        .punch-circle video,
        .punch-circle img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scaleX(-1);
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            object-fit: cover;
        }
    </style>

    <div class="pt-2 pb-16">
        <div class="min-h-screen bg-red-50 flex flex-col mx-2 shadow-2xl rounded-lg">

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Main Content -->
            <main class="flex-grow flex flex-col items-center justify-center p-4">
                <!-- Time Display -->
                <div class="text-center mb-8">
                    <p class="text-danger">{{$message}}</p>
                </div>

                <!-- Punch Circle -->

                <!-- Action Buttons -->
                <div class="w-full max-w-xs">
                    <form
                        action="{{route('attendance.user.note', ['record' => $record->id, 'type' => $type])}}"
                        method="post"  class="mt-3">
                        @csrf
                        <textarea class="form-control mb-2" name="note" id="" cols="30" rows="3" placeholder="write a note">{{old('note')}}</textarea>

                        <div class="d-grid">
                            <button type="submit" id="upload"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>Submit</button>
                        </div>
                    </form>

{{--                    <a href="{{route('attendance.day-wise')}}"--}}
{{--                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full flex items-center justify-center">--}}
{{--                        <i class="fas fa-redo mr-2"></i> Skip--}}
{{--                    </a>--}}
                </div>
            </main>
        </div>
    </div>


@endsection
