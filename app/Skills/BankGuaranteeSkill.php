<?php

namespace App\Skills;

use Modules\Finance\Models\BankGuarantee;

class BankGuaranteeSkill extends AbstractSkill
{
    protected string $name = 'Bank Guarantee Skill';
    protected string $description = 'Handles BG lifecycle, validity tracking, and classification.';

    /**
     * Create a new Bank Guarantee.
     */
    public function createBG(\Modules\Projects\Models\Project $project, array $data): BankGuarantee
    {
        return $project->bankGuarantees()->create([
            'bg_no' => $data['bg_no'] ?? null,
            'bg_date' => $data['bg_date'] ?? null,
            'amount' => $data['amount'],
            'type' => $data['type'], // ABG, PBG, AMC-BG
            'validity_date' => $data['validity_date'],
            'status' => 'active'
        ]);
    }

    /**
     * Check if a BG is expiring within a certain range.
     */
    public function isExpiringSoon(BankGuarantee $bg, int $days = 30): bool
    {
        $expiryDate = \Carbon\Carbon::parse($bg->validity_date);
        $diff = now()->diffInDays($expiryDate, false);
        return $diff >= 0 && $diff <= $days;
    }

    /**
     * Get active BGs for a project.
     */
    public function getActiveBGs(\Modules\Projects\Models\Project $project): \Illuminate\Database\Eloquent\Collection
    {
        return $project->bankGuarantees()->where('status', 'active')->get();
    }

    /**
     * Get BGs by type (ABG, PBG, AMC).
     */
    public function getBGsByType(\Modules\Projects\Models\Project $project, string $type): \Illuminate\Database\Eloquent\Collection
    {
        return $project->bankGuarantees()->where('type', $type)->get();
    }
}
