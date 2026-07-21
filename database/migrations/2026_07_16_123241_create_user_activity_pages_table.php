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
        Schema::create('user_activity_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_activity_id')
                ->constrained('user_activities')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            $table->string('route_name')->nullable()->index();
            $table->string('page_title')->nullable();
            $table->text('page_url')->nullable();

            $table->unsignedInteger('visit_count')->default(1);
            $table->unsignedBigInteger('active_seconds')->default(0);

            $table->timestamp('first_visited_at')->nullable();
            $table->timestamp('last_visited_at')->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'last_visited_at',
            ]);

            $table->index([
                'office_id',
                'last_visited_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_pages');
    }
};
