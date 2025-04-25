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
        Schema::table('half_days', function (Blueprint $table) {
            $table->unsignedBigInteger('respond_by')->nullable();
            $table->foreign('respond_by')->references('id')->on('users')->onDelete('SET NULL');
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('half_days', function (Blueprint $table) {
            //
        });
    }
};
