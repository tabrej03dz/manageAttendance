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
        Schema::table('offices', function (Blueprint $table) {
            $table->integer('number_of_employees')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('price_per_employee', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            //
        });
    }
};
