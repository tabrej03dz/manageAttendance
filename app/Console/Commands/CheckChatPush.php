<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckChatPush extends Command
{
    protected $signature = 'chat:check-push {--user= : Receiver user ID}';
    protected $description = 'Check chat push configuration without sending a notification or exposing credentials';

    public function handle(): int
    {
        $rows = [];
        $ok = true;
        $check = function (string $name, bool $passed, string $detail) use (&$rows, &$ok) {
            $rows[] = [$name, $passed ? 'OK' : 'FAIL', $detail];
            $ok = $ok && $passed;
        };
        $check('FCM enabled', (bool) config('chat_notifications.fcm_enabled'), 'Set CHAT_FCM_ENABLED=true');
        $check('Queue connection', config('queue.connections.chat_push.driver') === 'database', 'chat_push must use the database driver');
        $check('Google auth package', class_exists(\Google\Auth\Credentials\ServiceAccountCredentials::class), 'Install dependencies with composer install');
        $path = config('chat_notifications.credentials');
        $readable = is_string($path) && is_readable($path);
        $check('Credentials file', $readable, 'Configure a readable service-account JSON file');
        if ($readable) {
            try {
                $credentials = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
                $valid = is_array($credentials) && ($credentials['type'] ?? '') === 'service_account'
                    && !empty($credentials['client_email']) && !empty($credentials['private_key']);
                $check('Service account', $valid, 'Requires type, client_email and private_key');
                $project = config('chat_notifications.project_id') ?: ($credentials['project_id'] ?? null);
                $check('Project ID', is_string($project) && (bool) preg_match('/^[a-z][a-z0-9-]{4,62}$/', $project), 'Must match the mobile Firebase project');
            } catch (\Throwable $e) {
                $check('Credentials JSON', false, 'Cannot parse credentials; no credential contents displayed');
            }
        }
        try {
            foreach (['chat_devices', 'chat_notifications', 'chat_push_jobs', 'failed_jobs'] as $table) {
                $check($table, Schema::hasTable($table), 'Run php artisan migrate --force if missing');
            }
            if (Schema::hasTable('chat_push_jobs')) {
                $rows[] = ['Queued chat jobs', 'INFO', (string) DB::table('chat_push_jobs')->where('queue', 'chat-push')->count()];
            }
            if ($this->option('user') !== null) {
                $id = filter_var($this->option('user'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                $check('Receiver ID', $id !== false, 'Use a positive integer receiver user ID');
                if ($id !== false && Schema::hasTable('chat_devices')) {
                    $count = DB::table('chat_devices')->where('user_id', $id)->count();
                    $check('Receiver devices', $count > 0, $count . ' device(s); register through POST /api/chat/devices');
                }
            }
        } catch (\Throwable $e) {
            $check('Database access', false, 'Cannot query database; check server DB configuration');
        }
        $this->table(['Check', 'Result', 'Detail'], $rows);
        $this->line('This check does not test live FCM delivery or confirm that a worker is running.');
        $this->line('Worker: php artisan queue:work chat_push --queue=chat-push --tries=4 --timeout=75 -v');
        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
