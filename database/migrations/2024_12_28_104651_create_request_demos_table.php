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
        Schema::create('request_demos', function (Blueprint $table) {
            $table->id();
            $table->string('compan_name')->nullable();
            $table->string('owner_name');
            $table->string('number');
            $table->string('email')->nullable();
            $table->string('company_address')->nullable();
            $table->string('emp_size')->nullable();
            $table->string('designation')->nullable();
            $table->string('pin_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_demos');
    }
};
