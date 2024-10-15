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
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->text('check_in_note')->nullable();
            $table->enum('check_in_note_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('check_out_note')->nullable();
            $table->enum('check_out_note_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('check_in_note_response_by')->nullable();
            $table->unsignedBigInteger('check_out_note_response_by')->nullable();
            $table->foreign('check_in_note_response_by')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('check_out_note_response_by')->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            //
        });
    }
};
