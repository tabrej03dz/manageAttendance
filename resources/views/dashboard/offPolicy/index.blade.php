@extends('dashboard.layout.root')

@section('content')
    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Leave Policies</h1>

{{--            @can('create leave policy')--}}
                <a href="{{ route('off_policy.create') }}"
                   class="bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                    Create Leave Policy
                </a>
{{--            @endcan--}}
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">#</th>
                    <th class="py-3 px-6 text-left">Office Name</th>
                    <th class="py-3 px-6 text-left">Weekly Off Policy</th>
                    <th class="py-3 px-6 text-left">Actions</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                @foreach($offPolicies as $policy)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                        <td class="py-3 px-6 text-left">{{ $policy->office->name }}</td>
                        <td class="py-3 px-6 text-left">
                            @php
                                $weeklyOffPolicy = json_decode($policy->weekly_off_policy, true);
                            @endphp
                            <ul>
                                @foreach($weeklyOffPolicy as $week => $data)
                                    <li>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $week)) }}:</strong>
                                        {{ $data['off_count'] }} off(s)
                                        @if(!empty($data['off_days']))
                                            ({{ implode(', ', $data['off_days']) }})
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
{{--                        <td class="py-3 px-6 text-left flex space-x-2">--}}
{{--                            @can('edit leave policy')--}}
{{--                                <a title="Edit" href="{{ route('leave-policy.edit', ['leave_policy' => $policy->id]) }}"--}}
{{--                                   class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">--}}
{{--                                    <span class="material-icons">edit</span>--}}
{{--                                </a>--}}
{{--                            @endcan--}}

{{--                            @can('delete leave policy')--}}
{{--                                <a title="Delete" href="{{ route('leave-policy.delete', ['leave_policy' => $policy->id]) }}"--}}
{{--                                   class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">--}}
{{--                                    <span class="material-icons">delete</span>--}}
{{--                                </a>--}}
{{--                            @endcan--}}
{{--                        </td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
