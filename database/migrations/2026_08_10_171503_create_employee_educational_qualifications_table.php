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
        Schema::create('employee_educational_qualifications', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Employee
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Educational Qualification
            |--------------------------------------------------------------------------
            */

            $table->string('qualification');

            $table->string('course_name')->nullable();

            $table->string('board_university')->nullable();

            $table->string('institute_name')->nullable();

            $table->unsignedSmallInteger('passing_year')->nullable();

            $table->string('result', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Marksheet / Degree Document
            |--------------------------------------------------------------------------
            |
            | Is column me actual file nahi, uska storage path save hoga.
            |
            | Example:
            | employee_qualifications/25/graduation_degree.pdf
            |
            */

            $table->string('document_path')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Optional Document Type
            |--------------------------------------------------------------------------
            |
            | Isse pata chalega uploaded document Marksheet hai ya Degree.
            |
            */

            $table->string('document_type', 50)->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'user_id',
                'passing_year',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_educational_qualifications');
    }
};