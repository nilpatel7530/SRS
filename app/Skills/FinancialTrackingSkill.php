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

    /**
     * Get monthly actual billing for a financial year.
     */
    public function getMonthlyActuals(Project $project, string $financialYear): array
    {
        // Parse FY e.g. "2026-27"
        $parts = explode('-', $financialYear);
        if (count($parts) !== 2) return array_fill_keys(['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'], 0);

        $startYear = (int)$parts[0];
        $endYear = $startYear + 1;

        $months = [
            'apr' => ['m' => 4, 'y' => $startYear],
            'may' => ['m' => 5, 'y' => $startYear],
            'jun' => ['m' => 6, 'y' => $startYear],
            'jul' => ['m' => 7, 'y' => $startYear],
            'aug' => ['m' => 8, 'y' => $startYear],
            'sep' => ['m' => 9, 'y' => $startYear],
            'oct' => ['m' => 10, 'y' => $startYear],
            'nov' => ['m' => 11, 'y' => $startYear],
            'dec' => ['m' => 12, 'y' => $startYear],
            'jan' => ['m' => 1, 'y' => $endYear],
            'feb' => ['m' => 2, 'y' => $endYear],
            'mar' => ['m' => 3, 'y' => $endYear],
        ];

        $actuals = [];
        $total = 0;

        foreach ($months as $key => $config) {
            $amount = $project->invoices()
                ->whereYear('invoice_date', $config['y'])
                ->whereMonth('invoice_date', $config['m'])
                ->sum('cel_total');
            $actuals[$key] = $amount;
            $total += $amount;
        }

        $actuals['total_actual'] = $total;
        return $actuals;
    }
}

