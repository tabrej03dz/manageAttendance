<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('push_sent_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'message_id']);
            $table->index(['user_id', 'id']);
            $table->index(['push_sent_at', 'expires_at']);
        });
        Schema::create('chat_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_id', 128);
            $table->string('platform', 10);
            $table->text('fcm_token');
            $table->string('token_hash', 64)->unique();
            $table->timestamps();
            $table->unique(['user_id', 'device_id']);
        });
        Schema::create('chat_push_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('chat_push_jobs');
        Schema::dropIfExists('chat_devices');
        Schema::dropIfExists('chat_notifications');
    }
};
