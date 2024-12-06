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
        Schema::create('correction_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('attendance_record_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table ->text('note');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('attendance_record_id')->references('id')->on('attendance_records')->onDelete('SET NULL');
            $table->foreign('parent_id')->references('id')->on('correction_notes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correction_notes');
    }
};
