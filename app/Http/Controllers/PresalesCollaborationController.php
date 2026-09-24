<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresalesCollaboration;
use Illuminate\Support\Carbon;

class PresalesCollaborationController extends Controller
{
    /**
     * List of Enterprise BDMs for selection by Sales (Raiza)
     */
    public static function getBdmList(): array
    {
        return [
            [
                'name' => 'Budi Santoso',
                'role' => 'BDM Lead - Enterprise & Banking',
                'email' => 'budi.santoso@perusahaan.com',
                'avatar' => 'BS',
                'color' => 'bg-indigo-600',
            ],
            [
                'name' => 'Hendra Gunawan',
                'role' => 'BDM Specialist - FinTech & Payment',
                'email' => 'hendra.gunawan@perusahaan.com',
                'avatar' => 'HG',
                'color' => 'bg-sky-600',
            ],
            [
                'name' => 'Siti Rahmawati',
                'role' => 'BDM - Government & BUMN',
                'email' => 'siti.rahma@perusahaan.com',
                'avatar' => 'SR',
                'color' => 'bg-emerald-600',
            ],
            [
                'name' => 'Irfan Pratama',
                'role' => 'BDM - Telco & High-Tech Cloud',
                'email' => 'irfan.pratama@perusahaan.com',
                'avatar' => 'IP',
                'color' => 'bg-amber-600',
            ],
        ];
    }

    /**
     * Display the main collaboration & verification dashboard
     */
    public function index(Request $request)
    {
        $collaboration = PresalesCollaboration::first();
        $bdmList = self::getBdmList();

        if (!$collaboration) {
            $collaboration = PresalesCollaboration::create([
                'project_code' => 'PRJ-2026-BCA-009',
                'project_name' => 'Pengadaan Infrastruktur Payment Gateway & Core DC Switching',
                'client_name' => 'PT Bank Nusantara Digital Tbk',
                'assigned_by' => 'Raiza',
                'assigned_at' => Carbon::now()->subHours(2),
                'budget_estimation' => 'Rp 850.000.000',
                'assigned_bdm_reviewer' => 'Budi Santoso (BDM Lead - Enterprise & Banking)',
                'assigned_bdm_email' => 'budi.santoso@perusahaan.com',
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
                'sales_person_name' => 'Raiza (Account Executive)',
            ]);
        }

        // Active role for testing in view (sales, bdm, presales, sa)
        $activeRole = $request->query('role', 'sales');

        return view('collaboration.index', compact('collaboration', 'activeRole', 'bdmList'));
    }

    /**
     * Sales (Raiza) updates Team Assignment and selects BDM Reviewer
     */
    public function updateAssignment(Request $request, $id)
    {
        $collaboration = PresalesCollaboration::findOrFail($id);

        $request->validate([
            'presales_name' => 'required|string|max:100',
            'presales_instructions' => 'required|string',
            'sa_name' => 'required|string|max:100',
            'sa_instructions' => 'required|string',
            'assigned_bdm_reviewer' => 'required|string',
        ]);

        // Find email corresponding to selected BDM
        $bdmList = self::getBdmList();
        $selectedEmail = 'bdm.review@perusahaan.com';
        foreach ($bdmList as $bdm) {
            if (str_contains($request->assigned_bdm_reviewer, $bdm['name'])) {
                $selectedEmail = $bdm['email'];
                break;
            }
        }

        $collaboration->update([
            'presales_name' => $request->presales_name,
            'presales_instructions' => $request->presales_instructions,
            'sa_name' => $request->sa_name,
            'sa_instructions' => $request->sa_instructions,
            'assigned_bdm_reviewer' => $request->assigned_bdm_reviewer,
            'assigned_bdm_email' => $selectedEmail,
            'assigned_by' => 'Raiza',
            'assigned_at' => Carbon::now(),
        ]);

        return redirect()->route('collaboration.index', ['role' => $request->input('current_role', 'sales')])
            ->with('success', 'Penugasan tim teknis & penunjukan Reviewer BDM berhasil diperbarui oleh Raiza!');
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
        $collaboration->presales_notes = $request->input('presales_notes', 'Draft Proposal Teknis dan Bill of Quantity (BoQ) lengkap dengan rincian harga.');
        $collaboration->presales_status = 'submitted_to_bdm';
        $collaboration->presales_submitted_at = Carbon::now();

        // Update BDM Status to 'under_review' (Sedang Diverifikasi BDM)
        $collaboration->bdm_status = 'under_review';
        $collaboration->bdm_review_notes = null;
        $collaboration->save();

        return redirect()->route('collaboration.index', ['role' => $request->input('current_role', 'presales')])
            ->with('success', 'Proposal Teknis Pre-Sales berhasil disubmit ke BDM (' . $collaboration->assigned_bdm_reviewer . '). Status berubah menjadi "Sedang Diverifikasi BDM".');
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
        $collaboration->sa_notes = $request->input('sa_notes', 'Diagram Arsitektur High Availability & Network Sizing telah divalidasi.');
        $collaboration->sa_status = 'submitted_to_bdm';
        $collaboration->sa_submitted_at = Carbon::now();

        // Update BDM Status to 'under_review' (Sedang Diverifikasi BDM)
        $collaboration->bdm_status = 'under_review';
        $collaboration->bdm_review_notes = null;
        $collaboration->save();

        return redirect()->route('collaboration.index', ['role' => $request->input('current_role', 'sa')])
            ->with('success', 'Desain Arsitektur SA berhasil disubmit ke BDM (' . $collaboration->assigned_bdm_reviewer . '). Status berubah menjadi "Sedang Diverifikasi BDM".');
    }

    /**
     * Action by BDM (Approve / Reject & Request Revision)
     */
    public function bdmAction(Request $request, $id)
    {
        $collaboration = PresalesCollaboration::findOrFail($id);
        $action = $request->input('action'); // 'approve' or 'revision'
        $reviewer = $request->input('bdm_name', $collaboration->assigned_bdm_reviewer ?: 'Budi Santoso (BDM Lead)');
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

            // Unlock for Sales (Raiza)!
            $collaboration->sales_status = 'ready_for_sales';

            $message = 'Verifikasi oleh ' . $reviewer . ' BERHASIL DISETUJUI! Paket proposal resmi dibuka & siap digunakan oleh Raiza (Sales).';
        } elseif ($action === 'revision') {
            $target = $request->input('revision_target', 'both');

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

            $message = 'Catatan revisi dari ' . $reviewer . ' telah dikirimkan ke Tim Teknis. Berkas ditahan untuk perbaikan.';
        } else {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $collaboration->save();

        return redirect()->route('collaboration.index', ['role' => $request->input('current_role', 'bdm')])
            ->with('success', $message);
    }

    /**
     * Sales (Raiza) delivers proposal package to client
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
            ->with('success', 'Proposal Lengkap Resmi Dikirimkan oleh Raiza (Account Executive) ke Klien PT Bank Nusantara Digital Tbk.');
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

        return redirect()->route('collaboration.index', ['role' => 'sales'])
            ->with('info', 'Status demo telah di-reset ke kondisi awal (Menunggu Penugasan Teknis & Upload).');
    }
}
