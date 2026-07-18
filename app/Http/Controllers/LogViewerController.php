<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogViewerController extends Controller
{
    /**
     * Log directory.
     */
    private function logDirectory(): string
    {
        return storage_path('logs');
    }

    /**
     * Sirf authorized admin ko access dena.
     */
    private function authorizeAccess(): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Apne project ke role ke hisaab se condition rakhein
        |--------------------------------------------------------------------------
        */

        if (
            method_exists($user, 'hasRole') &&
            !$user->hasAnyRole(['super_admin', 'super admin'])
        ) {
            abort(403, 'You are not authorized to view system logs.');
        }

        /*
         * Agar aap role check nahi karna chahte hain aur har logged-in
         * user ko access dena hai to upar wali if condition hata sakte hain.
         */
    }

    /**
     * Selected file ka secure path return karega.
     */
    private function resolveLogFile(string $file): string
    {
        $file = basename($file);

        if (!str_ends_with(strtolower($file), '.log')) {
            abort(404, 'Invalid log file.');
        }

        $path = $this->logDirectory() . DIRECTORY_SEPARATOR . $file;

        if (!File::exists($path) || !File::isFile($path)) {
            abort(404, 'Log file not found.');
        }

        return $path;
    }

    /**
     * Log viewer page.
     */
public function index(Request $request)
{
    $this->authorizeAccess();

    $logDirectory = $this->logDirectory();

    if (!File::isDirectory($logDirectory)) {
        File::makeDirectory($logDirectory, 0755, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Log files list
    |--------------------------------------------------------------------------
    | Latest modified file sabse upar rahegi.
    */

    $logFiles = collect(File::files($logDirectory))
        ->filter(function ($file) {
            return strtolower($file->getExtension()) === 'log';
        })
        ->sortByDesc(function ($file) {
            return $file->getMTime();
        })
        ->map(function ($file) {
            return [
                'name'        => $file->getFilename(),
                'size'        => $this->formatBytes($file->getSize()),
                'modified_at' => date('d-m-Y h:i:s A', $file->getMTime()),
                'timestamp'   => $file->getMTime(),
            ];
        })
        ->values();

    $selectedFile = basename(
        (string) $request->query(
            'file',
            $logFiles->first()['name'] ?? 'laravel.log'
        )
    );

    $search = trim((string) $request->query('search', ''));

    $perPage = (int) $request->query('per_page', 25);

    if (!in_array($perPage, [10, 25, 50, 100, 250], true)) {
        $perPage = 25;
    }

    $entries = collect();

    $selectedPath = $logDirectory . DIRECTORY_SEPARATOR . $selectedFile;

    if (
        str_ends_with(strtolower($selectedFile), '.log') &&
        File::exists($selectedPath) &&
        File::isFile($selectedPath)
    ) {
        $content = File::get($selectedPath);

        $entries = $this->parseLogEntries($content);

        /*
        |--------------------------------------------------------------------------
        | Latest entry first
        |--------------------------------------------------------------------------
        */

        $entries = $entries
            ->sortByDesc(function ($entry) {
                return $entry['timestamp_unix'];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $searchLower = mb_strtolower($search);

            $entries = $entries
                ->filter(function ($entry) use ($searchLower) {
                    $searchable = implode(' ', [
                        $entry['datetime'],
                        $entry['date'],
                        $entry['time'],
                        $entry['environment'],
                        $entry['level'],
                        $entry['message'],
                        $entry['details'],
                    ]);

                    return str_contains(
                        mb_strtolower($searchable),
                        $searchLower
                    );
                })
                ->values();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    $currentPage = max((int) $request->query('page', 1), 1);
    $totalEntries = $entries->count();
    $lastPage = max((int) ceil($totalEntries / $perPage), 1);

    if ($currentPage > $lastPage) {
        $currentPage = $lastPage;
    }

    $paginatedEntries = $entries
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

    return view('logs.index', [
        'logFiles'     => $logFiles,
        'selectedFile' => $selectedFile,
        'entries'      => $paginatedEntries,
        'search'       => $search,
        'perPage'      => $perPage,
        'currentPage'  => $currentPage,
        'lastPage'     => $lastPage,
        'totalEntries' => $totalEntries,

        'firstItem' => $totalEntries > 0
            ? (($currentPage - 1) * $perPage) + 1
            : 0,

        'lastItem' => min(
            $currentPage * $perPage,
            $totalEntries
        ),
    ]);
}

    /**
     * Log file download.
     */
    public function download(string $file): BinaryFileResponse
    {
        $this->authorizeAccess();

        $path = $this->resolveLogFile($file);

        return response()->download($path, basename($path));
    }

    /**
     * Ek log file ko empty karega.
     */
    public function clear(string $file)
    {
        $this->authorizeAccess();

        $path = $this->resolveLogFile($file);

        File::put($path, '');

        return redirect()
            ->route('logs.index', ['file' => basename($path)])
            ->with('success', basename($path) . ' successfully cleared.');
    }

    /**
     * Ek log file delete karega.
     */
    public function destroy(string $file)
    {
        $this->authorizeAccess();

        $path = $this->resolveLogFile($file);

        File::delete($path);

        return redirect()
            ->route('logs.index')
            ->with('success', basename($path) . ' successfully deleted.');
    }

    /**
     * Saari log files delete karega.
     */
    public function destroyAll()
    {
        $this->authorizeAccess();

        $logDirectory = $this->logDirectory();

        if (File::isDirectory($logDirectory)) {
            collect(File::files($logDirectory))
                ->filter(function ($file) {
                    return strtolower($file->getExtension()) === 'log';
                })
                ->each(function ($file) {
                    File::delete($file->getPathname());
                });
        }

        /*
         * Laravel ke liye empty laravel.log dobara bana rahe hain.
         */
        File::put($logDirectory . DIRECTORY_SEPARATOR . 'laravel.log', '');

        return redirect()
            ->route('logs.index', ['file' => 'laravel.log'])
            ->with('success', 'All log files successfully deleted.');
    }

    /**
     * File size readable banayega.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $power = min(
            (int) floor(log($bytes, 1024)),
            count($units) - 1
        );

        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }


    /**
     * Laravel log content ko complete entries me parse karega.
     */
    private function parseLogEntries(string $content)
    {
        if (trim($content) === '') {
            return collect();
        }

        /*
        * Laravel standard log format:
        *
        * [2026-07-18 10:45:22] local.ERROR: Message...
        *
        * Multiline stack trace bhi same entry me rahega.
        */
        $pattern = '/^\[(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\]\s+([a-zA-Z0-9_-]+)\.([a-zA-Z]+):\s*(.*)$/m';

        preg_match_all(
            $pattern,
            $content,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $entries = collect();

        /*
        * Standard Laravel timestamp nahi mila to
        * normal custom log lines ko bhi handle karega.
        */
        if (empty($matches[0])) {
            return collect(
                preg_split('/\r\n|\r|\n/', $content)
            )
                ->filter(function ($line) {
                    return trim($line) !== '';
                })
                ->map(function ($line, $index) {
                    return [
                        'id'             => $index + 1,
                        'datetime'       => '-',
                        'date'           => '-',
                        'time'           => '-',
                        'environment'    => '-',
                        'level'          => 'INFO',
                        'message'        => trim($line),
                        'details'        => '',
                        'timestamp_unix' => $index,
                    ];
                })
                ->sortByDesc('timestamp_unix')
                ->values();
        }

        $totalMatches = count($matches[0]);

        for ($index = 0; $index < $totalMatches; $index++) {
            $fullMatch = $matches[0][$index][0];
            $startPosition = $matches[0][$index][1];

            $nextPosition = isset($matches[0][$index + 1])
                ? $matches[0][$index + 1][1]
                : strlen($content);

            $completeEntry = substr(
                $content,
                $startPosition,
                $nextPosition - $startPosition
            );

            $datetime = $matches[1][$index][0];
            $environment = strtoupper($matches[2][$index][0]);
            $level = strtoupper($matches[3][$index][0]);
            $firstMessage = trim($matches[4][$index][0]);

            /*
            * First line ko hata kar remaining stack trace/details.
            */
            $details = trim(
                substr($completeEntry, strlen($fullMatch))
            );

            try {
                $dateTimeObject = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $datetime
                );

                $formattedDate = $dateTimeObject->format('d-m-Y');
                $formattedTime = $dateTimeObject->format('h:i:s A');
                $timestampUnix = $dateTimeObject->timestamp;
            } catch (\Throwable $exception) {
                $formattedDate = '-';
                $formattedTime = '-';
                $timestampUnix = 0;
            }

            $entries->push([
                'id'             => $index + 1,
                'datetime'       => $datetime,
                'date'           => $formattedDate,
                'time'           => $formattedTime,
                'environment'    => $environment,
                'level'          => $level,
                'message'        => $firstMessage,
                'details'        => $details,
                'timestamp_unix' => $timestampUnix,
            ]);
        }

        return $entries;
    }
}