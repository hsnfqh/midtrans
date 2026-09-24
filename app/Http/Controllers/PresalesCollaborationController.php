<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresalesCollaboration;
use Illuminate\Support\Carbon;

class PresalesCollaborationController extends Controller
{
    /**
     * Display the main collaboration & verification dashboard
     */
    public function index(Request $request)
    {
        // Ensure default initial record exists matching the user's screenshot
        $collaboration = PresalesCollaboration::first();

        if (!$collaboration) {
            $collaboration = PresalesCollaboration::create([
                'project_code' => 'PRJ-2026-BCA-009',
                'project_name' => 'Pengadaan Infrastruktur Payment Gateway & Core DC Switching',
                'client_name' => 'PT Bank Nusantara Digital Tbk',
                'assigned_by' => 'Raiza',
                'assigned_at' => Carbon::now()->subHours(2),
                'budget_estimation' => 'Rp 850.000.000',
                'presales_name' => 'Akbar',
                'presales_role' => 'Pre-Sales Specialist',
                'presales_instructions' => 'Mohon segera dibuatkan proposal teknis dan BoQ estimasi proyek.',
                'presales_status' => 'pending_upload',
                'sa_name' => 'Aris Sadewo',
                'sa_role' => 'Solution Architect',
                'sa_instructions' => 'Mohon dirancang diagram topologi arsitektur sistem dan validasi sizing teknis.',
                'sa_status' => 'pending_upload',
                'bdm_status' => 'pending_submission',
                'sales_status' => 'locked_waiting_bdm',
                'sales_person_name' => 'Devi Anindya (Account Executive)',
            ]);
        }

        // Selected active role in view (bdm, presales, sa, sales)
        $activeRole = $request->query('role', 'bdm');

        return view('collaboration.index', compact('collaboration', 'activeRole'));
    }

    /**
     * Submit/Upload from Pre-Sales Specialist (Akbar)
     */
    public function uploadPresales(Request $request, $id)
    {
        $collaboration = PresalesCollaboration::findOrFail($id);

        $request->validate([
            'presales_notes' => 'nullable|string',
            'presales_file' => 'nullable|file|mimes:pdf,docx,xlsx,zip|max:20480',
        ]);

        $fileName = 'Proposal_Teknis_BoQ_v1.0_' . time() . '.pdf';
        if ($request->hasFile('presales_file')) {
            $file = $request->file('presales_file');
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('proposals', $fileName, 'public');
            $collaboration->presales_file_path = $path;
        } else {
            $collaboration->presales_file_path = 'proposals/' . $fileName;
        }

        $collaboration->presales_file_name = $fileName;
        $collaboration->presales_notes = $request->input('presales_notes', 'Draft Proposal Teknis dan Bill of Quantity (BoQ) lengkap dengan spesifikasi perangkat server & lisensi gateway.');
        $collaboration->presales_status = 'submitted_to_bdm';
        $collaboration->presales_submitted_at = Carbon::now();

        // Update BDM Status to 'under_review' (Sedang Diverifikasi BDM)
        $collaboration->bdm_status = 'under_review';
        $collaboration->bdm_review_notes = null; // reset notes if resubmitted
        $collaboration->save();

        return redirect()->route('collaboration.index', ['role' => $request->input('current_role', 'presales')])
            ->with('success', 'Berkas Pre-Sales berhasil diunggah! Status berubah menjadi "Sedang Diverifikasi BDM".');
    }

    /**
     * Submit/Upload from Solution Architect (Aris Sadewo)
     */
    public function uploadSa(Request $request, $id)
    {
        $collaboration = PresalesCollaboration::findOrFail($id);

        $request->validate([
            'sa_notes' => 'nullable|string',
            'sa_file' => 'nullable|file|mimes:pdf,png,jpg,drawio,vsdx,zip|max:20480',
        ]);

        $fileName = 'High_Level_Design_Topology_v1.0_' . time() . '.pdf';
        if ($request->hasFile('sa_file')) {
            $file = $request->file('sa_file');
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('topology', $fileName, 'public');
            $collaboration->sa_file_path = $path;
        } else {
            $collaboration->sa_file_path = 'topology/' . $fileName;
        }

        $collaboration->sa_file_name = $fileName;
        $collaboration->sa_notes = $request->input('sa_notes', 'Diagram Arsitektur High Availability (HA) Multi-Zone, Network Topology & Sizing Server valid.');
        $collaboration->sa_status = 'submitted_to_bdm';
        $collaboration->sa_submitted_at = Carbon::now();

        // Update BDM Status to 'under_review' (Sedang Diverifikasi BDM)
        $collaboration->bdm_status = 'under_review';
        $collaboration->bdm_review_notes = null; // reset notes if resubmitted
        $collaboration->save();

        return redirect()->route('collaboration.index', ['role' => $request->input('current_role', 'sa')])
            ->with('success', 'Desain Arsitektur & Topologi SA berhasil diunggah! Status berubah menjadi "Sedang Diverifikasi BDM".');
    }

    /**
     * Action by BDM (Approve / Reject & Request Revision)
     */
    public function bdmAction(Request $request, $id)
    {
        $collaboration = PresalesCollaboration::findOrFail($id);
        $action = $request->input('action'); // 'approve' or 'revision'
        $reviewer = $request->input('bdm_name', 'Budi Santoso (BDM Lead)');
        $notes = $request->input('bdm_notes');

        if ($action === 'approve') {
            $collaboration->bdm_status = 'approved';
            $collaboration->bdm_reviewed_by = $reviewer;
            $collaboration->bdm_reviewed_at = Carbon::now();
            $collaboration->bdm_review_notes = $notes ?: 'Dokumen teknis & komersial telah diverifikasi dan disetujui. Sizing dan estimasi BoQ sudah sesuai standar.';

            // Upgrade statuses
            if ($collaboration->presales_status === 'submitted_to_bdm') {
                $collaboration->presales_status = 'approved_by_bdm';
            }
            if ($collaboration->sa_status === 'submitted_to_bdm') {
                $collaboration->sa_status = 'approved_by_bdm';
            }

            // Unlock for Sales!
            $collaboration->sales_status = 'ready_for_sales';

            $message = 'Verifikasi BDM BERHASIL DISETUJUI! Paket proposal resmi dibuka & siap digunakan oleh Tim Sales.';
        } elseif ($action === 'revision') {
            $target = $request->input('revision_target', 'both'); // 'presales', 'sa', 'both'

            $collaboration->bdm_status = 'revision_needed';
            $collaboration->bdm_reviewed_by = $reviewer;
            $collaboration->bdm_reviewed_at = Carbon::now();
            $collaboration->bdm_review_notes = $notes ?: 'Mohon lakukan penyesuaian sizing server dan diskon margin komersial BoQ.';

            if (in_array($target, ['presales', 'both'])) {
                $collaboration->presales_status = 'revision_required';
            }
            if (in_array($target, ['sa', 'both'])) {
                $collaboration->sa_status = 'revision_required';
            }

            // Sales remains locked
            $collaboration->sales_status = 'locked_waiting_bdm';

            $message = 'Catatan revisi telah dikirimkan ke Tim Teknis (Presales/SA). Berkas ditahan untuk perbaikan.';
        } else {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $collaboration->save();

        return redirect()->route('collaboration.index', ['role' => $request->input('current_role', 'bdm')])
            ->with('success', $message);
    }

    /**
     * Sales delivers / downloads final proposal package to client
     */
    public function salesSend(Request $request, $id)
    {
        $collaboration = PresalesCollaboration::findOrFail($id);

        if ($collaboration->sales_status !== 'ready_for_sales' && $collaboration->sales_status !== 'sent_to_client') {
            return back()->with('error', 'Dokumen belum disetujui oleh BDM! Tidak dapat dikirim ke klien.');
        }

        $collaboration->sales_status = 'sent_to_client';
        $collaboration->sales_delivered_at = Carbon::now();
        $collaboration->save();

        return redirect()->route('collaboration.index', ['role' => 'sales'])
            ->with('success', 'Proposal Lengkap Resmi Dikirimkan ke Klien PT Bank Nusantara Digital Tbk.');
    }

    /**
     * Reset Demo data to initial state for testing convenience
     */
    public function resetDemo($id)
    {
        $collaboration = PresalesCollaboration::findOrFail($id);
        $collaboration->update([
            'presales_status' => 'pending_upload',
            'presales_file_name' => null,
            'presales_file_path' => null,
            'presales_notes' => null,
            'presales_submitted_at' => null,
            'sa_status' => 'pending_upload',
            'sa_file_name' => null,
            'sa_file_path' => null,
            'sa_notes' => null,
            'sa_submitted_at' => null,
            'bdm_status' => 'pending_submission',
            'bdm_reviewed_by' => null,
            'bdm_reviewed_at' => null,
            'bdm_review_notes' => null,
            'sales_status' => 'locked_waiting_bdm',
            'sales_delivered_at' => null,
        ]);

        return redirect()->route('collaboration.index')
            ->with('info', 'Status demo telah di-reset ke kondisi awal (Menunggu Berkas).');
    }
}
