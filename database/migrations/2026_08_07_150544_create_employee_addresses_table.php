<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('premise_details')->nullable();
            $table->string('street_road')->nullable();
            $table->string('locality_area')->nullable();
            $table->string('landmark')->nullable();
            $table->string('city', 150)->nullable();
            $table->string('district', 150)->nullable();
            $table->string('state', 150)->nullable();
            $table->string('pin_code', 6)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_addresses');
    }
};