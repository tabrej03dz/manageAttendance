<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained('chat_conversations')
                ->cascadeOnDelete();

            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('message')->nullable();

            // Future me image/pdf/file bhejne ke liye
            $table->string('attachment')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_type')->nullable();

            // Future reply feature
            $table->foreignId('reply_to_id')
                ->nullable()
                ->constrained('chat_messages')
                ->nullOnDelete();

            $table->timestamp('edited_at')->nullable();

            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
