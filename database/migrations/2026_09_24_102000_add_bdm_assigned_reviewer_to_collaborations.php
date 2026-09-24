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
        Schema::table('presales_collaborations', function (Blueprint $table) {
            $table->string('assigned_bdm_reviewer')->default('Budi Santoso (BDM Lead - Enterprise)')->after('budget_estimation');
            $table->string('assigned_bdm_email')->default('budi.santoso@perusahaan.com')->after('assigned_bdm_reviewer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presales_collaborations', function (Blueprint $table) {
            $table->dropColumn(['assigned_bdm_reviewer', 'assigned_bdm_email']);
        });
    }
};
