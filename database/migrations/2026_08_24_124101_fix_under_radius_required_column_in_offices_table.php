<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert the existing radius setting to a real boolean column.
     */
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table
                ->boolean('under_radius_required_new')
                ->default(false)
                ->after('under_radius_required');
        });

        DB::table('offices')
            ->select(['id', 'under_radius_required'])
            ->orderBy('id')
            ->chunkById(100, function ($offices) {
                foreach ($offices as $office) {
                    $value = strtolower(
                        trim((string) $office->under_radius_required)
                    );

                    DB::table('offices')
                        ->where('id', $office->id)
                        ->update([
                            'under_radius_required_new' => in_array(
                                $value,
                                [
                                    '1',
                                    'true',
                                    'yes',
                                    'on',
                                    'enable',
                                    'enabled',
                                    'required',
                                ],
                                true
                            ),
                        ]);
                }
            });

        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('under_radius_required');
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->renameColumn(
                'under_radius_required_new',
                'under_radius_required'
            );
        });
    }

    /**
     * Restore the old yes/no representation if the migration is rolled back.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table
                ->enum('under_radius_required_old', ['yes', 'no'])
                ->default('no')
                ->after('under_radius_required');
        });

        DB::table('offices')
            ->select(['id', 'under_radius_required'])
            ->orderBy('id')
            ->chunkById(100, function ($offices) {
                foreach ($offices as $office) {
                    DB::table('offices')
                        ->where('id', $office->id)
                        ->update([
                            'under_radius_required_old' =>
                                (bool) $office->under_radius_required
                                    ? 'yes'
                                    : 'no',
                        ]);
                }
            });

        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn('under_radius_required');
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->renameColumn(
                'under_radius_required_old',
                'under_radius_required'
            );
        });
    }
};