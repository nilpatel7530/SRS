<?php

namespace App\Skills;

use Modules\Projects\Models\Project;
use Modules\Finance\Models\BankGuarantee;
use Modules\Finance\Models\Invoice;
use App\Models\User;

class DashboardAggregationSkill extends AbstractSkill
{
    protected string $name = 'Dashboard Aggregation Skill';
    protected string $description = 'Handles KPI aggregation and user-specific portfolio views.';

    /**
     * Get aggregate KPIs for the system or user.
     */
    public function getKPIs(?User $user = null): array
    {
        $cacheKey = 'dashboard_kpis_' . ($user ? $user->id : 'system');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($user) {
            $projectQuery = Project::query();
            
            $bgQuery = BankGuarantee::where('status', 'active');
            $invoiceQuery = Invoice::where('status', 'pending');

            if ($user && !$user->isAdmin()) {
                $projectIds = $user->projects()->pluck('projects.id');
                $projectQuery->whereIn('id', $projectIds);
                $bgQuery->whereIn('project_id', $projectIds);
                $invoiceQuery->whereIn('project_id', $projectIds);
            }

            return [
                'total_projects' => $projectQuery->count(),
                'active_bgs' => $bgQuery->count(),
                'pending_invoices' => $invoiceQuery->count(),
                'budget_utilization' => rand(30, 80), // Mocked
            ];
        });
    }
}
