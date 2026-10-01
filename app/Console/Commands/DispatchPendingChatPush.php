<?php
namespace App\Console\Commands;
use App\Jobs\SendChatPush;
use App\Models\ChatNotification;
use Illuminate\Console\Command;
class DispatchPendingChatPush extends Command
{
    protected $signature = 'chat:dispatch-pending-push';
    protected $description = 'Recover chat notifications whose push job was not queued';
    public function handle(): int
    {
        ChatNotification::query()->whereNull('read_at')->whereNull('push_sent_at')
            ->where('expires_at', '>', now())->select('id')->chunkById(100, function ($rows) {
                foreach ($rows as $row) { SendChatPush::dispatch((int) $row->id); }
            });
        $this->info('Pending chat push jobs queued.');
        return self::SUCCESS;
    }
}
