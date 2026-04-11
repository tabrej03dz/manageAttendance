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
        Schema::create('employee_rosters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('duty_date');

            // working = duty, off = weekly/custom off, half_day = half shift, leave = pre-approved leave
            $table->enum('status', ['working', 'off', 'half_day', 'leave'])->default('working');

            $table->string('shift_name')->nullable(); // optional
            $table->time('start_time')->nullable();   // optional
            $table->time('end_time')->nullable();     // optional
            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'duty_date']);

            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_rosters');
    }
};
