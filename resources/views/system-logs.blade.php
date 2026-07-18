<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Laravel System Logs</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">

    <div class="mx-auto max-w-7xl px-4 py-8">

        <div
            class="mb-5 flex flex-col gap-4 rounded-2xl
                   border border-slate-800 bg-slate-900 p-5
                   sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold">
                    Laravel System Logs
                </h1>

                <p class="mt-1 text-sm text-slate-400">
                    Laravel log ki latest 500 lines dikhai ja rahi hain.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('system.logs') }}"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm
                           font-semibold text-white transition
                           hover:bg-blue-500"
                >
                    Refresh Logs
                </a>

                <a
                    href="{{ route('home') }}"
                    class="rounded-lg border border-slate-700
                           bg-slate-800 px-4 py-2 text-sm
                           font-semibold text-white transition
                           hover:bg-slate-700"
                >
                    Dashboard
                </a>

            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-black">

            <div
                class="flex items-center justify-between border-b
                       border-slate-800 bg-slate-900 px-5 py-3"
            >
                <span class="text-sm font-medium text-slate-300">
                    storage/logs/laravel.log
                </span>

                <span class="rounded-full bg-emerald-500/10 px-3 py-1
                             text-xs font-semibold text-emerald-400">
                    Latest logs
                </span>
            </div>

            <pre
                id="logContainer"
                class="max-h-[75vh] overflow-auto whitespace-pre-wrap
                       break-words p-5 font-mono text-xs leading-6
                       text-emerald-300"
            >{{ $logs }}</pre>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logContainer = document.getElementById('logContainer');

            if (logContainer) {
                logContainer.scrollTop = logContainer.scrollHeight;
            }
        });
    </script>

</body>
</html>