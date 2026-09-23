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
        Schema::create('designations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Department
            |--------------------------------------------------------------------------
            |
            | Example:
            | Sales Department -> Sales Executive
            | HR Department    -> HR Manager
            |
            */

            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Designation Details
            |--------------------------------------------------------------------------
            */

            $table->string('name', 150);

            // Example: SE, TL, HRM, SM
            $table->string('code', 50)
                ->nullable();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Hierarchy Level
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | 1 = Director
            | 2 = Manager
            | 3 = Team Leader
            | 4 = Senior Executive
            | 5 = Executive
            |
            */

            $table->unsignedInteger('level')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Sort Order
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Created By
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Soft Delete
            |--------------------------------------------------------------------------
            */

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('name');
            $table->index('is_active');
            $table->index('level');

            /*
            |--------------------------------------------------------------------------
            | Unique Designation
            |--------------------------------------------------------------------------
            |
            | Same department me same designation repeat nahi hogi.
            |
            */

            $table->unique(
                ['office_id', 'department_id', 'name'],
                'designations_office_department_name_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};