@extends('dashboard.layout.root')
@section('content')

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        @if ($policy)
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">{{ $policy->title }}</h1>

                <a href="{{ route('policy.accept', ['policy' => $policy->id]) }}"
                   class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                    Yes, I Agree!
                </a>
            </div>

            <div class="overflow-x-auto">
                {!! $policy->description !!}
            </div>
        @else
            <div class="text-center py-8">
                <h2 class="text-xl font-semibold text-gray-500">No Policy Record</h2>
            </div>
        @endif
    </div>

@endsection
