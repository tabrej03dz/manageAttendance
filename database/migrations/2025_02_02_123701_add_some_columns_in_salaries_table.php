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
        Schema::table('salaries', function (Blueprint $table) {
//            $table->string('office_days')->nullable();
////            $table->string('working_days')->nullable();
//            $table->string('office_closed_days')->nullable();
//            $table->string('paid_leave')->nullable();
//            $table->string('unpaid_leave')->nullable();
//            $table->string('late_deduction')->nullable();
//            $table->string('other_deduction')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            //
        });
    }
};
