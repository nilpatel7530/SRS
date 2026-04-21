<?php

namespace App\Skills;

use Modules\Projects\Models\Project;

class FinancialTrackingSkill extends AbstractSkill
{
    protected string $name = 'Financial Tracking Skill';
    protected string $description = 'Handles Capex / Opex logic, aggregation and projections.';

    /**
     * Add CAPEX entry.
     */
    public function addCapexEntry(Project $project, array $data): \Modules\Finance\Models\CapexEntry
    {
        return $project->capexEntries()->create([
            'amount' => $data['amount'],
            'remarks' => $data['remarks'] ?? $data['description'] ?? null,
            'completion_date' => $data['completion_date'] ?? $data['entry_date'] ?? now(),
        ]);
    }

    /**
     * Add OPEX entry.
     */
    public function addOpexEntry(Project $project, array $data): \Modules\Finance\Models\OpexEntry
    {
        return $project->opexEntries()->create([
            'amount' => $data['amount'],
            'remarks' => $data['remarks'] ?? $data['description'] ?? null,
            'duration' => $data['duration'] ?? null,
            'entry_date' => $data['entry_date'] ?? now(),
        ]);
    }

    /**
     * Calculate total capex for a project.
     */
    public function getTotalCapex(Project $project): float
    {
        return $project->capexEntries()->sum('amount');
    }

    /**
     * Calculate total opex for a project.
     */
    public function getTotalOpex(Project $project): float
    {
        return $project->opexEntries()->sum('amount');
    }

    /**
     * Get aggregated financial overview for a project.
     */
    public function getProjectFinancials(Project $project): array
    {
        $capex = $this->getTotalCapex($project);
        $opex = $this->getTotalOpex($project);
        $total = $capex + $opex;

        // Projections: Total + pending invoices + 10% buffers
        $pendingInvoiceAmount = $project->invoices()->where('status', 'pending')->sum('total_amount');
        
        return [
            'total_capex' => $capex,
            'total_opex' => $opex,
            'grand_total' => $total,
            'pending_invoices' => $pendingInvoiceAmount,
            'projections' => $total + $pendingInvoiceAmount + ($total * 0.10),
        ];
    }

    /**
     * Create or update project target for a specific financial year.
     */
    public function updateProjectTarget(Project $project, array $data): \Modules\Projects\Models\ProjectTarget
    {
        return $project->projectTargets()->updateOrCreate(
            ['financial_year' => $data['financial_year']],
            [
                'billed_prev_fy' => $data['billed_prev_fy'] ?? 0,
                'apr' => $data['apr'] ?? 0,
                'may' => $data['may'] ?? 0,
                'jun' => $data['jun'] ?? 0,
                'jul' => $data['jul'] ?? 0,
                'aug' => $data['aug'] ?? 0,
                'sep' => $data['sep'] ?? 0,
                'oct' => $data['oct'] ?? 0,
                'nov' => $data['nov'] ?? 0,
                'dec' => $data['dec'] ?? 0,
                'jan' => $data['jan'] ?? 0,
                'feb' => $data['feb'] ?? 0,
                'mar' => $data['mar'] ?? 0,
                'remarks' => $data['remarks'] ?? null,
            ]
        );
    }
}

