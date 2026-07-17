<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Browser session identification
            |--------------------------------------------------------------------------
            */

            $table->uuid('activity_uuid')->unique();
            $table->string('laravel_session_id')->nullable()->index();

            /*
            |--------------------------------------------------------------------------
            | Activity timing
            |--------------------------------------------------------------------------
            */

            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamp('ended_at')->nullable();

            // User ne actual kitne seconds software use kiya
            $table->unsignedBigInteger('active_seconds')->default(0);

            // User ne kitne pages open kiye
            $table->unsignedInteger('page_views')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Current page information
            |--------------------------------------------------------------------------
            */

            $table->string('current_route')->nullable();
            $table->text('current_url')->nullable();
            $table->string('current_page_title')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Device information
            |--------------------------------------------------------------------------
            */

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Online status
            |--------------------------------------------------------------------------
            |
            | active  = user currently software chala raha hai
            | idle    = software open hai, lekin activity nahi kar raha
            | ended   = browser/tab/session close
            |
            */

            $table->enum('status', [
                'active',
                'idle',
                'ended',
            ])->default('active')->index();

            $table->timestamps();

            $table->index([
                'user_id',
                'started_at',
            ]);

            $table->index([
                'office_id',
                'started_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
