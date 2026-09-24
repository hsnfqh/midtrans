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
        Schema::create('presales_collaborations', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->string('project_name');
            $table->string('client_name');
            $table->string('assigned_by')->default('Raiza (BDM Lead)');
            $table->timestamp('assigned_at')->nullable();
            $table->string('budget_estimation')->nullable();

            // Pre-Sales Specialist fields
            $table->string('presales_name')->default('Akbar');
            $table->string('presales_role')->default('Pre-Sales Specialist');
            $table->text('presales_instructions')->nullable();
            $table->string('presales_file_name')->nullable();
            $table->string('presales_file_path')->nullable();
            $table->text('presales_notes')->nullable();
            $table->string('presales_status')->default('pending_upload'); // pending_upload, submitted_to_bdm, revision_required, approved_by_bdm
            $table->timestamp('presales_submitted_at')->nullable();

            // Solution Architect fields
            $table->string('sa_name')->default('Aris Sadewo');
            $table->string('sa_role')->default('Solution Architect');
            $table->text('sa_instructions')->nullable();
            $table->string('sa_file_name')->nullable();
            $table->string('sa_file_path')->nullable();
            $table->text('sa_notes')->nullable();
            $table->string('sa_status')->default('pending_upload'); // pending_upload, submitted_to_bdm, revision_required, approved_by_bdm
            $table->timestamp('sa_submitted_at')->nullable();

            // BDM Verification & Approval fields
            $table->string('bdm_status')->default('pending_submission'); // pending_submission, under_review, revision_needed, approved
            $table->string('bdm_reviewed_by')->nullable();
            $table->timestamp('bdm_reviewed_at')->nullable();
            $table->text('bdm_review_notes')->nullable();

            // Sales Dashboard Delivery fields
            $table->string('sales_status')->default('locked_waiting_bdm'); // locked_waiting_bdm, ready_for_sales, sent_to_client
            $table->string('sales_person_name')->default('Devi Anindya (Account Executive)');
            $table->timestamp('sales_delivered_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presales_collaborations');
    }
};
