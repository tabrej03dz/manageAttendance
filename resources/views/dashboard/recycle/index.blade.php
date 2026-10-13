
@extends('dashboard.layout.root')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="bg-gray-100 p-4 rounded-lg shadow-md">

    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Recycle Bin
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Manage deleted employees and employee letters.
            </p>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

        <a href="{{ route('recycle.index', ['tab' => 'employees']) }}"
           class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex justify-between items-center">

            <div>
                <p class="text-gray-500 text-sm">Deleted Employees</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1">
                    {{ $deletedUsersCount }}
                </h2>
            </div>

            <div class="bg-red-100 text-red-600 rounded-full p-3">
                <span class="material-icons">people</span>
            </div>
        </a>

        <a href="{{ route('recycle.index', ['tab' => 'letters']) }}"
           class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex justify-between items-center">

            <div>
                <p class="text-gray-500 text-sm">Deleted Employee Letters</p>
                <h2 class="text-3xl font-bold text-gray-800 mt-1">
                    {{ $deletedLettersCount }}
                </h2>
            </div>

            <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                <span class="material-icons">description</span>
            </div>
        </a>

    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-3 border-b border-gray-300 mb-5">

        <a href="{{ route('recycle.index', ['tab' => 'employees']) }}"
           class="px-5 py-3 font-semibold flex items-center gap-2 transition
           {{ $activeTab === 'employees'
               ? 'text-blue-600 border-b-2 border-blue-600'
               : 'text-gray-500 hover:text-blue-600' }}">

            <span class="material-icons text-lg">people</span>
            Employees
            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">
                {{ $deletedUsersCount }}
            </span>
        </a>

        <a href="{{ route('recycle.index', ['tab' => 'letters']) }}"
           class="px-5 py-3 font-semibold flex items-center gap-2 transition
           {{ $activeTab === 'letters'
               ? 'text-blue-600 border-b-2 border-blue-600'
               : 'text-gray-500 hover:text-blue-600' }}">

            <span class="material-icons text-lg">description</span>
            Employee Letters
            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">
                {{ $deletedLettersCount }}
            </span>
        </a>

    </div>

    {{-- Employees Tab --}}
    @if($activeTab === 'employees')

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-800">
                    Deleted Employees
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">

                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <th class="py-4 px-5 text-left">#</th>
                            <th class="py-4 px-5 text-left">Name</th>
                            <th class="py-4 px-5 text-left">Email</th>
                            <th class="py-4 px-5 text-left">Phone</th>
                            <th class="py-4 px-5 text-left">Deleted At</th>
                            <th class="py-4 px-5 text-left">Action</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-700 text-sm">

                        @forelse($users as $user)

                            <tr class="border-b border-gray-100 hover:bg-gray-50">

                                <td class="py-4 px-5">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>

                                <td class="py-4 px-5 font-semibold">
                                    {{ $user->name }}
                                </td>

                                <td class="py-4 px-5">
                                    {{ $user->email ?? '-' }}
                                </td>

                                <td class="py-4 px-5">
                                    {{ $user->phone ?? '-' }}
                                </td>

                                <td class="py-4 px-5">
                                    {{ $user->deleted_at
                                        ? $user->deleted_at->format('d M Y h:i A')
                                        : '-' }}
                                </td>

                                <td class="py-4 px-5">

                                    <div class="flex items-center gap-2">

                                        @can('restore employee')
                                            <form method="POST"
                                                  action="{{ route('recycle.user.restore', ['user' => $user->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to restore this employee?')">

                                                @csrf

                                                <button type="submit"
                                                        title="Restore Employee"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg shadow-sm transition">
                                                    <span class="material-icons">restore</span>
                                                </button>
                                            </form>
                                        @endcan

                                        @can('permanent delete employee')
                                            <form method="POST"
                                                  action="{{ route('recycle.user.delete', ['user' => $user->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to permanently delete this employee? This action cannot be undone.')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        title="Permanently Delete Employee"
                                                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-sm transition">
                                                    <span class="material-icons">delete_forever</span>
                                                </button>
                                            </form>
                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">

                                    <span class="material-icons text-5xl text-gray-300">
                                        inventory_2
                                    </span>

                                    <p class="mt-2 font-medium">
                                        No deleted employees found.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $users->appends(['tab' => 'employees'])->links() }}
            </div>

        </div>

    @endif

    {{-- Employee Letters Tab --}}
    @if($activeTab === 'letters')

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="font-bold text-lg text-gray-800">
                    Deleted Employee Letters
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">

                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs">

                            <th class="py-4 px-5 text-left">#</th>
                            <th class="py-4 px-5 text-left">Letter ID</th>
                            <th class="py-4 px-5 text-left">Employee</th>
                            <th class="py-4 px-5 text-left">Letter Title</th>
                            <th class="py-4 px-5 text-left">Letter Type</th>
                            <th class="py-4 px-5 text-left">Deleted At</th>
                            <th class="py-4 px-5 text-left">Action</th>

                        </tr>
                    </thead>

                    <tbody class="text-gray-700 text-sm">

                        @forelse($employeeLetters as $letter)

                            <tr class="border-b border-gray-100 hover:bg-gray-50">

                                <td class="py-4 px-5">
                                    {{ $employeeLetters->firstItem() + $loop->index }}
                                </td>

                                <td class="py-4 px-5 font-semibold">
                                    #{{ $letter->id }}
                                </td>

                                <td class="py-4 px-5">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ $letter->user?->name ?? 'Employee not available' }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            {{ $letter->user?->email ?? '' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="py-4 px-5">
                                    {{ $letter->title
                                        ?? $letter->subject
                                        ?? $letter->letter_title
                                        ?? 'Employee Letter' }}
                                </td>

                                <td class="py-4 px-5">

                                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ $letter->letter_type
                                            ?? $letter->type
                                            ?? 'Letter' }}
                                    </span>

                                </td>

                                <td class="py-4 px-5">
                                    {{ $letter->deleted_at
                                        ? $letter->deleted_at->format('d M Y h:i A')
                                        : '-' }}
                                </td>

                                <td class="py-4 px-5">

                                    <div class="flex items-center gap-2">

                                       

                                            <form method="POST"
                                                  action="{{ route('recycle.employee-letter.restore', ['letter' => $letter->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to restore this employee letter?')">

                                                @csrf

                                                <button type="submit"
                                                        title="Restore Letter"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg shadow-sm transition">

                                                    <span class="material-icons">
                                                        restore
                                                    </span>

                                                </button>

                                            </form>

                                       

                                            <form method="POST"
                                                  action="{{ route('recycle.employee-letter.delete', ['letter' => $letter->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to permanently delete this letter? This action cannot be undone.')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        title="Permanently Delete Letter"
                                                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-sm transition">

                                                    <span class="material-icons">
                                                        delete_forever
                                                    </span>

                                                </button>

                                            </form>


                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">

                                    <span class="material-icons text-5xl text-gray-300">
                                        description
                                    </span>

                                    <p class="mt-2 font-medium">
                                        No deleted employee letters found.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $employeeLetters->appends(['tab' => 'letters'])->links() }}
            </div>

        </div>

    @endif

</div>

@endsection
