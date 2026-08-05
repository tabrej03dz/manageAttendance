<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('leave_authority_id')
                ->nullable()
                ->after('team_leader_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(
                ['office_id', 'leave_authority_id'],
                'users_office_leave_authority_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_office_leave_authority_index');
            $table->dropConstrainedForeignId('leave_authority_id');
        });
    }
};