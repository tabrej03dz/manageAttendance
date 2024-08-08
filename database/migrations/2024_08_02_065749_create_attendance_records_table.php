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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('check_in')->nullable();
            $table->string('check_in_image')->nullable();
            $table->decimal('duration', 8,2)->nullable();
            $table->dateTime('check_out')->nullable();
            $table->string('check_out_image')->nullable();
            $table->enum('day_type', ['half day', 'full_day','leave', 'holiday', '__'])->default('__');
            $table->decimal('check_in_distance', 6,2)->nullable();
            $table->decimal('check_out_distance', 6,2)->nullable();
            $table->integer('late')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
