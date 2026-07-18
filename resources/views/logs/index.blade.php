<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Laravel Log Viewer</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .log-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .log-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .log-scrollbar::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 20px;
        }

        .dark .log-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }
    </style>

    <script>
        tailwind.config = {
            darkMode: 'class'
        };

        if (localStorage.getItem('log-viewer-theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-900 transition dark:bg-slate-950 dark:text-slate-100">

<div class="min-h-screen">

    {{-- Header --}}
    <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto flex max-w-[1600px] flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow">
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5a3 3 0 016 0"
                        />
                    </svg>
                </div>

                <div>
                    <h1 class="text-xl font-black">
                        Laravel Log Viewer
                    </h1>

                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        View, search, download, clear and delete log files
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                <button
                    type="button"
                    onclick="toggleDarkMode()"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                    </svg>

                    Dark Mode
                </button>

                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-slate-700 dark:bg-blue-600 dark:hover:bg-blue-500"
                >
                    Dashboard
                </a>

            </div>
        </div>
    </header>

    <main class="mx-auto max-w-[1600px] px-4 py-5 sm:px-6">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-5 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                <span>{{ session('success') }}</span>

                <button
                    type="button"
                    onclick="this.parentElement.remove()"
                    class="text-xl leading-none"
                >
                    &times;
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-800 dark:bg-red-950/50 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-5 lg:grid-cols-[300px_minmax(0,1fr)]">

            {{-- Left log files --}}
            <aside class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800">
                    <h2 class="font-black">
                        Log Files
                    </h2>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ $logFiles->count() }} log file(s) available
                    </p>
                </div>

                <div class="log-scrollbar max-h-[70vh] overflow-y-auto p-2">

                    @forelse($logFiles as $logFile)

                        <a
                            href="{{ route('logs.index', ['file' => $logFile['name']]) }}"
                            class="mb-1 block rounded-xl border px-3 py-3 transition
                                {{ $selectedFile === $logFile['name']
                                    ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300'
                                    : 'border-transparent hover:border-slate-200 hover:bg-slate-50 dark:hover:border-slate-700 dark:hover:bg-slate-800'
                                }}"
                        >
                            <div class="flex items-start gap-3">

                                <svg
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"
                                    />
                                </svg>

                                <div class="min-w-0 flex-1">
                                    <div class="break-all text-sm font-bold">
                                        {{ $logFile['name'] }}
                                    </div>

                                    <div class="mt-1 flex flex-wrap gap-x-2 text-[11px] opacity-70">
                                        <span>{{ $logFile['size'] }}</span>
                                        <span>•</span>
                                        <span>{{ $logFile['modified_at'] }}</span>
                                    </div>
                                </div>

                            </div>
                        </a>

                    @empty

                        <div class="p-6 text-center text-sm text-slate-500">
                            No log files found.
                        </div>

                    @endforelse

                </div>
            </aside>

            {{-- Main log content --}}
            <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">

                {{-- Top toolbar --}}
                <div class="border-b border-slate-200 p-4 dark:border-slate-800">

                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                        <div>
                            <div class="flex flex-wrap items-center gap-2">

                                <h2 class="break-all text-lg font-black">
                                    {{ $selectedFile }}
                                </h2>

                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                    {{ number_format($totalLines) }} lines
                                </span>

                            </div>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Latest log entries are displayed first
                            </p>
                        </div>

                        <form
                            method="GET"
                            action="{{ route('logs.index') }}"
                            class="flex flex-col gap-2 sm:flex-row"
                        >
                            <input
                                type="hidden"
                                name="file"
                                value="{{ $selectedFile }}"
                            >

                            <select
                                name="per_page"
                                onchange="this.form.submit()"
                                class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800"
                            >
                                @foreach([10, 25, 50, 100, 250] as $option)
                                    <option
                                        value="{{ $option }}"
                                        @selected($perPage === $option)
                                    >
                                        {{ $option }} entries
                                    </option>
                                @endforeach
                            </select>

                            <div class="relative">
                                <input
                                    type="search"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Search logs..."
                                    class="w-full rounded-xl border border-slate-300 bg-white py-2 pl-10 pr-4 text-sm outline-none focus:border-blue-500 sm:w-72 dark:border-slate-700 dark:bg-slate-800"
                                >

                                <svg
                                    class="absolute left-3 top-2.5 h-5 w-5 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"
                                    />
                                </svg>
                            </div>

                            <button
                                type="submit"
                                class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-500"
                            >
                                Search
                            </button>

                            @if($search !== '')
                                <a
                                    href="{{ route('logs.index', [
                                        'file' => $selectedFile,
                                        'per_page' => $perPage
                                    ]) }}"
                                    class="rounded-xl border border-slate-300 px-4 py-2 text-center text-sm font-bold transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800"
                                >
                                    Reset
                                </a>
                            @endif

                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="log-scrollbar max-h-[65vh] overflow-auto">
                    <table class="min-w-full border-collapse">

                        <thead class="sticky top-0 z-10 bg-slate-100 dark:bg-slate-800">
                        <tr>
                            <th class="w-28 border-b border-r border-slate-200 px-4 py-3 text-left text-xs font-black uppercase tracking-wide dark:border-slate-700">
                                Line
                            </th>

                            <th class="border-b border-slate-200 px-4 py-3 text-left text-xs font-black uppercase tracking-wide dark:border-slate-700">
                                Content
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">

                        @forelse($lines as $line)

                            @php
                                $content = $line['content'];

                                $rowClass = '';

                                if (str_contains(strtolower($content), 'error')) {
                                    $rowClass = 'bg-red-50 text-red-800 dark:bg-red-950/30 dark:text-red-300';
                                } elseif (str_contains(strtolower($content), 'warning')) {
                                    $rowClass = 'bg-amber-50 text-amber-800 dark:bg-amber-950/30 dark:text-amber-300';
                                } elseif (str_contains(strtolower($content), 'success')) {
                                    $rowClass = 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300';
                                }
                            @endphp

                            <tr class="{{ $rowClass }}">
                                <td class="border-r border-slate-200 px-4 py-3 align-top font-mono text-xs font-bold dark:border-slate-800">
                                    {{ $line['line_number'] }}
                                </td>

                                <td class="whitespace-pre-wrap break-all px-4 py-3 font-mono text-xs leading-6">
                                    {{ $line['content'] }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="2"
                                    class="px-5 py-16 text-center"
                                >
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                        <svg
                                            class="h-7 w-7 text-slate-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>

                                    <p class="mt-3 font-bold">
                                        No log entries found
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Log file empty hai ya search ka result nahi mila.
                                    </p>
                                </td>
                            </tr>

                        @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="border-t border-slate-200 p-4 dark:border-slate-800">

                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">

                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Showing
                            <strong>{{ $firstItem }}</strong>
                            to
                            <strong>{{ $lastItem }}</strong>
                            of
                            <strong>{{ number_format($totalLines) }}</strong>
                            entries
                        </div>

                        {{-- Pagination --}}
                        <div class="flex flex-wrap items-center gap-1">

                            @if($currentPage > 1)
                                <a
                                    href="{{ route('logs.index', [
                                        'file' => $selectedFile,
                                        'search' => $search,
                                        'per_page' => $perPage,
                                        'page' => $currentPage - 1,
                                    ]) }}"
                                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800"
                                >
                                    Previous
                                </a>
                            @else
                                <span class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400 dark:border-slate-800">
                                    Previous
                                </span>
                            @endif

                            <span class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white">
                                {{ $currentPage }}
                            </span>

                            <span class="px-2 text-sm text-slate-500">
                                of {{ $lastPage }}
                            </span>

                            @if($currentPage < $lastPage)
                                <a
                                    href="{{ route('logs.index', [
                                        'file' => $selectedFile,
                                        'search' => $search,
                                        'per_page' => $perPage,
                                        'page' => $currentPage + 1,
                                    ]) }}"
                                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800"
                                >
                                    Next
                                </a>
                            @else
                                <span class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400 dark:border-slate-800">
                                    Next
                                </span>
                            @endif

                        </div>
                    </div>

                    {{-- Actions --}}
                    @if($logFiles->contains('name', $selectedFile))
                        <div class="mt-5 flex flex-wrap gap-2 border-t border-slate-200 pt-4 dark:border-slate-800">

                            <a
                                href="{{ route('logs.download', ['file' => $selectedFile]) }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-500"
                            >
                                Download File
                            </a>

                            <form
                                method="POST"
                                action="{{ route('logs.clear', ['file' => $selectedFile]) }}"
                                onsubmit="return confirmClearFile('{{ addslashes($selectedFile) }}')"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-amber-400"
                                >
                                    Clear File
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('logs.destroy', ['file' => $selectedFile]) }}"
                                onsubmit="return confirmDeleteFile('{{ addslashes($selectedFile) }}')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-500"
                                >
                                    Delete File
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('logs.destroy-all') }}"
                                onsubmit="return confirmDeleteAll()"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl border border-red-300 bg-red-50 px-4 py-2 text-sm font-bold text-red-700 transition hover:bg-red-100 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950"
                                >
                                    Delete All Files
                                </button>
                            </form>

                            <a
                                href="{{ route('logs.index', ['file' => $selectedFile]) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800"
                            >
                                Refresh
                            </a>

                        </div>
                    @endif

                </div>
            </section>
        </div>
    </main>
</div>

<script>
    function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');

        const theme = document.documentElement.classList.contains('dark')
            ? 'dark'
            : 'light';

        localStorage.setItem('log-viewer-theme', theme);
    }

    function confirmClearFile(fileName) {
        return confirm(
            'Kya aap "' + fileName + '" ka poora content clear karna chahte hain?\n\n' +
            'File delete nahi hogi, lekin uske saare logs remove ho jayenge.'
        );
    }

    function confirmDeleteFile(fileName) {
        return confirm(
            'Kya aap "' + fileName + '" ko permanently delete karna chahte hain?\n\n' +
            'Ye action undo nahi hoga.'
        );
    }

    function confirmDeleteAll() {
        return confirm(
            'WARNING!\n\n' +
            'Kya aap storage/logs ki saari .log files permanently delete karna chahte hain?\n\n' +
            'Ye action undo nahi hoga.'
        );
    }
</script>

</body>
</html>