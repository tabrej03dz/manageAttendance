<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignId('designation_id')
                ->nullable()
                ->after('department_id')
                ->constrained('designations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign([
                'designation_id'
            ]);

            $table->dropColumn('designation_id');
        });
    }
};