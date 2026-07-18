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

        $logFiles = collect(File::files($logDirectory))
            ->filter(function ($file) {
                return strtolower($file->getExtension()) === 'log';
            })
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            })
            ->map(function ($file) {
                return [
                    'name'          => $file->getFilename(),
                    'size'          => $this->formatBytes($file->getSize()),
                    'modified_at'   => date('d-m-Y h:i A', $file->getMTime()),
                    'modified_time' => $file->getMTime(),
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

        $lines = [];

        $selectedPath = $logDirectory . DIRECTORY_SEPARATOR . $selectedFile;

        if (
            str_ends_with(strtolower($selectedFile), '.log') &&
            File::exists($selectedPath) &&
            File::isFile($selectedPath)
        ) {
            $fileLines = file(
                $selectedPath,
                FILE_IGNORE_NEW_LINES
            ) ?: [];

            /*
             * Latest logs ko upar dikhane ke liye reverse.
             */
            $totalOriginalLines = count($fileLines);

            $lines = collect($fileLines)
                ->map(function ($content, $index) use ($totalOriginalLines) {
                    return [
                        'line_number' => $index + 1,
                        'content'     => $content,
                    ];
                })
                ->reverse()
                ->values();

            if ($search !== '') {
                $lines = $lines->filter(function ($line) use ($search) {
                    return str_contains(
                        mb_strtolower($line['content']),
                        mb_strtolower($search)
                    );
                })->values();
            }
        } else {
            $lines = collect();
        }

        $currentPage = max((int) $request->query('page', 1), 1);
        $totalLines  = $lines->count();
        $lastPage    = max((int) ceil($totalLines / $perPage), 1);

        if ($currentPage > $lastPage) {
            $currentPage = $lastPage;
        }

        $paginatedLines = $lines
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values();

        return view('logs.index', [
            'logFiles'       => $logFiles,
            'selectedFile'   => $selectedFile,
            'lines'          => $paginatedLines,
            'search'         => $search,
            'perPage'        => $perPage,
            'currentPage'    => $currentPage,
            'lastPage'       => $lastPage,
            'totalLines'     => $totalLines,
            'firstItem'      => $totalLines > 0
                ? (($currentPage - 1) * $perPage) + 1
                : 0,
            'lastItem'       => min($currentPage * $perPage, $totalLines),
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
}