<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PresalesCollaboration;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PresalesCollaborationTest extends TestCase
{
    public function test_collaboration_dashboard_loads_correctly(): void
    {
        $response = $this->get('/collaboration');
        $response->assertStatus(200);
        $response->assertSee('Kolaborasi Teknis Solusi (Presales & Solution Architect)', false);
        $response->assertSee('Gerbang Verifikasi BDM Reviewer', false);
    }

    public function test_sales_can_update_team_assignment_and_select_bdm(): void
    {
        $collaboration = PresalesCollaboration::first();
        
        $response = $this->post("/collaboration/{$collaboration->id}/assignment", [
            'presales_name' => 'Akbar Fauzan',
            'presales_instructions' => 'Mohon siapkan BoQ Tier-3 Data Center.',
            'sa_name' => 'Aris Sadewo',
            'sa_instructions' => 'Rancang arsitektur HA Active-Active.',
            'assigned_bdm_reviewer' => 'Budi Santoso (BDM Lead - Enterprise & Banking)',
        ]);

        $response->assertRedirect();
        
        $collaboration->refresh();
        $this->assertEquals('Akbar Fauzan', $collaboration->presales_name);
        $this->assertEquals('Budi Santoso (BDM Lead - Enterprise & Banking)', $collaboration->assigned_bdm_reviewer);
        $this->assertEquals('budi.santoso@perusahaan.com', $collaboration->assigned_bdm_email);
    }

    public function test_presales_upload_changes_status_to_under_review(): void
    {
        $collaboration = PresalesCollaboration::first();
        
        $response = $this->post("/collaboration/{$collaboration->id}/presales-upload", [
            'presales_notes' => 'Draft Proposal Teknis v1.0 telah selesai.',
        ]);

        $response->assertRedirect();
        
        $collaboration->refresh();
        $this->assertEquals('submitted_to_bdm', $collaboration->presales_status);
        $this->assertEquals('under_review', $collaboration->bdm_status);
    }

    public function test_sa_upload_changes_status_to_under_review(): void
    {
        $collaboration = PresalesCollaboration::first();
        
        $response = $this->post("/collaboration/{$collaboration->id}/sa-upload", [
            'sa_notes' => 'Diagram arsitektur & topologi telah disesuaikan.',
        ]);

        $response->assertRedirect();
        
        $collaboration->refresh();
        $this->assertEquals('submitted_to_bdm', $collaboration->sa_status);
        $this->assertEquals('under_review', $collaboration->bdm_status);
    }

    public function test_bdm_can_request_revision(): void
    {
        $collaboration = PresalesCollaboration::first();
        
        $response = $this->post("/collaboration/{$collaboration->id}/bdm-action", [
            'action' => 'revision',
            'bdm_name' => 'Budi Santoso (BDM Lead)',
            'bdm_notes' => 'Tolong hitung ulang margin diskon hardware.',
            'revision_target' => 'presales',
        ]);

        $response->assertRedirect();
        
        $collaboration->refresh();
        $this->assertEquals('revision_needed', $collaboration->bdm_status);
        $this->assertEquals('revision_required', $collaboration->presales_status);
        $this->assertEquals('locked_waiting_bdm', $collaboration->sales_status);
    }

    public function test_bdm_can_approve_and_unlock_for_sales(): void
    {
        $collaboration = PresalesCollaboration::first();
        
        // Simulate uploads
        $collaboration->update([
            'presales_status' => 'submitted_to_bdm',
            'sa_status' => 'submitted_to_bdm',
            'bdm_status' => 'under_review',
        ]);

        $response = $this->post("/collaboration/{$collaboration->id}/bdm-action", [
            'action' => 'approve',
            'bdm_name' => 'Budi Santoso (BDM Lead)',
            'bdm_notes' => 'Semua berkas dan BoQ telah valid dan disetujui.',
        ]);

        $response->assertRedirect();
        
        $collaboration->refresh();
        $this->assertEquals('approved', $collaboration->bdm_status);
        $this->assertEquals('approved_by_bdm', $collaboration->presales_status);
        $this->assertEquals('approved_by_bdm', $collaboration->sa_status);
        $this->assertEquals('ready_for_sales', $collaboration->sales_status);
    }

    public function test_sales_can_send_proposal_to_client_after_bdm_approval(): void
    {
        $collaboration = PresalesCollaboration::first();
        $collaboration->update([
            'sales_status' => 'ready_for_sales',
            'bdm_status' => 'approved',
        ]);

        $response = $this->post("/collaboration/{$collaboration->id}/sales-send");
        $response->assertRedirect();

        $collaboration->refresh();
        $this->assertEquals('sent_to_client', $collaboration->sales_status);
        $this->assertNotNull($collaboration->sales_delivered_at);
    }
}
