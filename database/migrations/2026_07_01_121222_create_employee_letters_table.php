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
        Schema::create('employee_letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_no')->unique();

            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->restrictOnDelete();

            $table->foreignId('letter_template_id')
                ->nullable()
                ->constrained('letter_templates')
                ->nullOnDelete();

            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            // Existing employee ho to user_id, new candidate ho to nullable
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Snapshot employee/candidate data
            $table->string('employee_name');
            $table->string('employee_email')->nullable();
            $table->string('employee_phone')->nullable();
            $table->string('designation')->nullable();
            $table->text('address')->nullable();

            $table->date('joining_date')->nullable();
            $table->decimal('salary', 12, 2)->nullable();

            // Letter issue date
            $table->date('issue_date');

            // Extra dynamic fields:
            // reporting_manager, monthly_target, working_hours, week_offs etc.
            $table->json('extra_data')->nullable();

            // Final generated subject and body snapshot
            // Template baad me edit ho tab bhi old generated letter same rahega
            $table->string('rendered_subject')->nullable();
            $table->longText('rendered_html');

            $table->enum('status', ['draft', 'issued', 'cancelled'])->default('issued');

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['department_id', 'document_type_id', 'issue_date'],
                'letters_filter_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_letters');
    }
};
