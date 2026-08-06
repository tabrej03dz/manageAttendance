<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'account_holder_name')) {
                $table->string('account_holder_name')
                    ->nullable()
                    ->after('account_number');
            }

            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name')
                    ->nullable()
                    ->after('account_holder_name');
            }

            if (!Schema::hasColumn('users', 'bank_branch')) {
                $table->string('bank_branch')
                    ->nullable()
                    ->after('bank_name');
            }

            if (!Schema::hasColumn('users', 'ifsc_code')) {
                $table->string('ifsc_code', 20)
                    ->nullable()
                    ->after('bank_branch');
            }

            if (!Schema::hasColumn('users', 'account_type')) {
                $table->enum('account_type', [
                    'savings',
                    'current',
                    'salary',
                    'other',
                ])
                    ->nullable()
                    ->after('ifsc_code');
            }

            if (!Schema::hasColumn('users', 'upi_id')) {
                $table->string('upi_id')
                    ->nullable()
                    ->after('account_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'account_holder_name',
                'bank_name',
                'bank_branch',
                'ifsc_code',
                'account_type',
                'upi_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};