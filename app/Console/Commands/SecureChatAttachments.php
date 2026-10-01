<?php
namespace App\Console\Commands;

use App\Models\ChatMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SecureChatAttachments extends Command
{
    protected $signature = 'chat:secure-attachments';
    protected $description = 'Move existing chat attachments from public to private storage';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $failed = false;
        ChatMessage::query()->whereNotNull('attachment')->select(['id', 'attachment'])
            ->chunkById(200, function ($messages) use ($public, $private, &$failed) {
                foreach ($messages as $message) {
                    $path = $message->attachment;
                    if (!str_starts_with($path, 'chat-attachments/') || str_contains($path, '..')) {
                        $this->warn("Skipped unexpected path for message {$message->id}.");
                        $failed = true;
                        continue;
                    }
                    if (!$public->exists($path)) {
                        if (!$private->exists($path)) {
                            $this->warn("Missing attachment for message {$message->id}.");
                            $failed = true;
                        }
                        continue;
                    }
                    // Always overwrite from the public source, then verify byte-for-byte.
                    $stream = $public->readStream($path);
                    try {
                        $saved = $stream && $private->put($path, $stream);
                    } finally {
                        if (is_resource($stream)) { fclose($stream); }
                    }
                    if (!$saved || hash('sha256', $public->get($path)) !== hash('sha256', $private->get($path))) {
                        $this->error("Copy failed for message {$message->id}; public copy retained.");
                        $failed = true;
                        continue;
                    }
                    if (!$public->delete($path)) {
                        $this->error("Could not remove public copy for message {$message->id}.");
                        $failed = true;
                    }
                }
            });
        if ($failed) { return self::FAILURE; }
        $this->info('All existing chat attachments are now private.');
        return self::SUCCESS;
    }
}
