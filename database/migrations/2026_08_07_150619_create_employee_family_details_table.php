<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_family_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('marital_status', [
                'single',
                'married',
                'divorced',
                'widowed',
                'separated',
            ])->default('single');

            $table->string('spouse_name')->nullable();
            $table->string('spouse_phone', 20)->nullable();
            $table->date('spouse_dob')->nullable();
            $table->string('spouse_occupation')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_family_details');
    }
};