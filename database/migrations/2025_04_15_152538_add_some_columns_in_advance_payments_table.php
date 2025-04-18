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
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->enum('status', ['requested', 'paid', 'rejected'])->default('requested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advance_payments', function (Blueprint $table) {
            //
        });
    }
};
