<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: temp column (skip if a previous failed run already created it)
        if (! Schema::hasColumn('offices', 'under_radius_required_new')) {
            Schema::table('offices', function (Blueprint $table) {
                $table->boolean('under_radius_required_new')->default(false);
            });
        }

        // Step 2: copy data, only if the old column still exists
        if (Schema::hasColumn('offices', 'under_radius_required')) {
            DB::table('offices')
                ->select(['id', 'under_radius_required'])
                ->orderBy('id')
                ->chunkById(100, function ($offices) {
                    foreach ($offices as $office) {
                        $value = strtolower(trim((string) $office->under_radius_required));

                        DB::table('offices')
                            ->where('id', $office->id)
                            ->update([
                                'under_radius_required_new' => in_array(
                                    $value,
                                    ['1', 'true', 'yes', 'on', 'enable', 'enabled', 'required'],
                                    true
                                ),
                            ]);
                    }
                });

            Schema::table('offices', function (Blueprint $table) {
                $table->dropColumn('under_radius_required');
            });
        }

        // Step 3: rename using raw SQL that works on old MariaDB/MySQL
        if (! Schema::hasColumn('offices', 'under_radius_required')) {
            DB::statement(
                'ALTER TABLE `offices` CHANGE `under_radius_required_new` `under_radius_required` TINYINT(1) NOT NULL DEFAULT 0'
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('offices', 'under_radius_required_old')) {
            Schema::table('offices', function (Blueprint $table) {
                $table->enum('under_radius_required_old', ['yes', 'no'])->default('no');
            });
        }

        DB::table('offices')
            ->select(['id', 'under_radius_required'])
            ->orderBy('id')
            ->chunkById(100, function ($offices) {
                foreach ($offices as $office) {
                    DB::table('offices')
                        ->where('id', $office->id)
                        ->update([
                            'under_radius_required_old' => $office->under_radius_required ? 'yes' : 'no',
                        ]);
                }
            });

        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('under_radius_required');
        });

        DB::statement(
            "ALTER TABLE `offices` CHANGE `under_radius_required_old` `under_radius_required` ENUM('yes','no') NOT NULL DEFAULT 'no'"
        );
    }
};
