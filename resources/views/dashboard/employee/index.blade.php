@extends('dashboard.layout.root')
@section('content')

    <style>
        #more-options {
            max-height: 0;
            overflow: hidden;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Employees</h1>

            <form action="{{ route('employee.index') }}" method="GET"
                  class="d-flex flex-column flex-md-row align-items-stretch">
                @csrf
                <select name="status" id="" onchange="this.form.submit()" class="form-control mb-2 mb-md-0 mr-md-2">
                    <option value="">Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                {{--                             <input type="submit" value="Filter" class="btn btn-success text-white mb-2">--}}
                <a href="{{ route('reports.index') }}" class="btn btn-info mb-2 ml-2">Clear</a>
            </form>

            @can('create employee')
            <a href="{{route('employee.create')}}"
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create
            </a>
{{--                <a href="{{route('departments.index')}}"--}}
{{--                   class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">--}}
{{--                    Departments--}}
{{--                </a>--}}
            @endcan
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Phone</th>
                        <th class="py-3 px-6 text-left">Image</th>
                        <th class="py-3 px-6 text-left">Office</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Location R</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach($employees as $employee)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{$loop->iteration}}</td>
                        <td class="py-3 px-6 text-left">{{$employee->name}}</td>
                        <td class="py-3 px-6 text-left">{{$employee->email}}</td>
                        <td class="py-3 px-6 text-left">{{$employee->phone}}</td>
                        <td class="py-3 px-6 text-left">
                            <img src="{{$employee->photo ? asset('storage/'. $employee->photo) : 'https://via.placeholder.com/50'}}" alt="{{$employee->name}}" class="rounded-full w-12 h-12">
                        </td>
                        <td class="py-3 px-6 text-left">{{$employee->office?->name}}</td>
                        <td class="py-3 px-6 text-left">
                            @can('change status of employee')
                            <a href="{{route('employee.status', ['employee' => $employee->id])}}" class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($employee->status == '1')
                                    bg-green-100 text-green-800">Active
                                @else
                                    bg-red-100 text-red-800">Inactive
                                @endif
                            </a>
                            @endcan
                        </td>
                        <td class="py-3 px-6 text-left">{{$employee->location_required}}</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            @can('edit employee')
                            <a title="Edit" href="{{ route('employee.edit', ['employee' => $employee->id]) }}"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </a>
                            @endcan
                            @can('delete employee')
                            <form action="{{ route('employee.delete', ['employee' => $employee->id]) }}" method="post">
                                @csrf
                                <button title="Delete" type="submit"
                                    class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                            @endcan
                            @can('show profile of employee')
                            <a title="Profile" href="{{ route('employee.profile', ['user' => $employee->id]) }}"
                                class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">account_circle</span>
                            </a>
                            @endcan
                            @role('super_admin|admin|owner')
                                <a title="Salary Setup" href="{{ route('salary.setupForm', ['employee' => $employee->id]) }}"
                                   class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                    <span class="material-icons">payments</span>
                                </a>
                            @endrole
                            @can('show permissions of employee')
                                <a title="Permissions" href="{{ route('employee.permission', ['user' => $employee->id]) }}"
                                   class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                    <span class="material-icons">admin_panel_settings</span>
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
