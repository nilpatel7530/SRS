<?php

namespace App\Skills;

use Modules\Projects\Models\Project;

class ReportingEngineSkill extends AbstractSkill
{
    protected string $name = 'Reporting Engine Skill';
    protected string $description = 'Handles dynamic filters, consolidated data building, and exports.';

    /**
     * Build reporting data based on filters.
     */
    public function buildReportData(array $filters = []): array
    {
        $query = Project::with(['department', 'capexEntries', 'opexEntries', 'bankGuarantees', 'invoices']);

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['financial_type'])) {
            $query->where('financial_type', $filters['financial_type']);
        }
        
        if (!empty($filters['project_type'])) {
            $query->where('project_type', $filters['project_type']);
        }

        return $query->get()->map(function($project) {
            $bg = $project->bankGuarantees->sortBy('validity_date')->first();
            
            // Month-wise Invoice calculation (Last 6 months)
            $monthWiseInvoices = [];
            for ($i = 0; $i < 6; $i++) {
                $monthStr = now()->subMonths($i)->format('Y-m');
                $monthWiseInvoices[$monthStr] = $project->invoices()
                    ->where('invoice_date', 'like', $monthStr . '%')
                    ->sum('total_amount');
            }

            // Simple Sales Projection for next 3 months (based on Opex burn rate or fixed projection)
            $recentAvg = $project->invoices()->where('invoice_date', '>=', now()->subMonths(3)->startOfMonth())->sum('total_amount') / 3;
            $projections = [
                now()->addMonth()->format('Y-m') => $recentAvg * 1.1, // 10% expected growth
                now()->addMonths(2)->format('Y-m') => $recentAvg * 1.15,
                now()->addMonths(3)->format('Y-m') => $recentAvg * 1.2,
            ];

            return [
                'project_name' => $project->name,
                'department' => $project->department->name ?? 'N/A',
                'financial_type' => strtoupper($project->financial_type ?? 'N/A'),
                'project_type' => strtoupper($project->project_type ?? 'N/A'),
                'total_capex' => $project->capexEntries->sum('amount'),
                'total_opex' => $project->opexEntries->sum('amount'),
                'total_invoiced' => $project->invoices->sum('total_amount'),
                'pending_invoices' => $project->invoices->where('status', 'pending')->sum('total_amount'),
                'bg_count' => $project->bankGuarantees->count(),
                'next_bg_expiry' => $bg ? $bg->validity_date : 'N/A',
                'bg_status' => $bg ? ucfirst($bg->status) : 'Missing',
                'month_wise' => $monthWiseInvoices,
                'projections' => $projections,
                'remarks' => $project->remarks ?? 'Consolidated Project View',
            ];
        })->toArray();
    }
}
