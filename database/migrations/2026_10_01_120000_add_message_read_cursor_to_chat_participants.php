<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_participants', function (Blueprint $table) {
            $table->unsignedBigInteger('last_read_message_id')->default(0);
        });
        // Preserve existing web read receipts. No DB-specific UPDATE JOIN syntax.
        DB::table('chat_participants')->whereNotNull('last_read_at')->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $lastId = DB::table('chat_messages')->where('conversation_id', $row->conversation_id)
                        ->where('created_at', '<=', $row->last_read_at)->max('id') ?? 0;
                    DB::table('chat_participants')->where('id', $row->id)
                        ->update(['last_read_message_id' => $lastId]);
                }
            });
    }
    public function down(): void
    {
        Schema::table('chat_participants', fn (Blueprint $table) => $table->dropColumn('last_read_message_id'));
    }
};
