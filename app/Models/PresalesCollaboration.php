<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresalesCollaboration extends Model
{
    use HasFactory;

    protected $table = 'presales_collaborations';

    protected $fillable = [
        'project_code',
        'project_name',
        'client_name',
        'assigned_by',
        'assigned_at',
        'budget_estimation',
        'assigned_bdm_reviewer',
        'assigned_bdm_email',
        'presales_name',
        'presales_role',
        'presales_instructions',
        'presales_file_name',
        'presales_file_path',
        'presales_notes',
        'presales_status',
        'presales_submitted_at',
        'sa_name',
        'sa_role',
        'sa_instructions',
        'sa_file_name',
        'sa_file_path',
        'sa_notes',
        'sa_status',
        'sa_submitted_at',
        'bdm_status',
        'bdm_reviewed_by',
        'bdm_reviewed_at',
        'bdm_review_notes',
        'sales_status',
        'sales_person_name',
        'sales_delivered_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'presales_submitted_at' => 'datetime',
        'sa_submitted_at' => 'datetime',
        'bdm_reviewed_at' => 'datetime',
        'sales_delivered_at' => 'datetime',
    ];

    /**
     * Check if ready for BDM review
     */
    public function isUnderBdmReview(): bool
    {
        return $this->bdm_status === 'under_review';
    }

    /**
     * Check if approved by BDM
     */
    public function isApprovedByBdm(): bool
    {
        return $this->bdm_status === 'approved';
    }

    /**
     * Check if needs revision
     */
    public function isRevisionNeeded(): bool
    {
        return $this->bdm_status === 'revision_needed';
    }
}
