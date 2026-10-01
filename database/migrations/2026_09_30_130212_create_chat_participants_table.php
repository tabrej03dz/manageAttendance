<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained('chat_conversations')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('last_read_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['conversation_id', 'user_id'],
                'chat_participant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_participants');
    }
};
