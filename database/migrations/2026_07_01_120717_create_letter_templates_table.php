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
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
           $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->cascadeOnDelete();

            // Template office-wise bhi ho sakta hai
            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            // Template department-wise save hoga
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('title');

            // Example: Offer of Employment - {{designation}}
            $table->string('subject')->nullable();

            // Letter ka full HTML content
            $table->longText('body_html');

            // Auto extracted variables: employee_name, salary etc.
            $table->json('variables')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['document_type_id', 'office_id', 'department_id', 'is_active'],
                'tpl_lookup_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
