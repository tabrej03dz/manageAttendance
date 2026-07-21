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
        Schema::table('user_activities', function (Blueprint $table) {
            $table->string('source')
                ->default('web')
                ->after('office_id')
                ->index();

            $table->string('app_version')
                ->nullable()
                ->after('source');

            $table->string('device_id')
                ->nullable()
                ->after('app_version')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            //
        });
    }
};
