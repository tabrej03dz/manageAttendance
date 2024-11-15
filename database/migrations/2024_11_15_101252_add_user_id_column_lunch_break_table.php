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
        Schema::table('lunch_breaks', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('start_image')->nullable();
            $table->string('start_distance')->nullable();
            $table->string('end_image')->nullable();
            $table->string('end_distance')->nullable();
            $table->text('reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lunch_breaks', function (Blueprint $table) {
            //
        });
    }
};
