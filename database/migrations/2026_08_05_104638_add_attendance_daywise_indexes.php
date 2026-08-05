<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->index(
                ['user_id', 'check_in'],
                'attendance_user_check_in_index'
            );

            $table->index(
                ['user_id', 'check_out'],
                'attendance_user_check_out_index'
            );
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->index(
                ['user_id', 'start_date', 'end_date'],
                'leave_user_dates_index'
            );
        });

        Schema::table('half_days', function (Blueprint $table) {
            $table->index(
                ['user_id', 'date'],
                'half_day_user_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('attendance_user_check_in_index');
            $table->dropIndex('attendance_user_check_out_index');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropIndex('leave_user_dates_index');
        });

        Schema::table('half_days', function (Blueprint $table) {
            $table->dropIndex('half_day_user_date_index');
        });
    }
};