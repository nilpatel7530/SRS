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

    public function getKPIs(?User $user = null): array
    {
        $cacheKey = 'dashboard_kpis_' . ($user ? $user->id : 'system');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($user) {
            $projectQuery = Project::query();
            
            if ($user && !$user->hasRole('Admin')) {
                // If not Admin, show projects user is assigned to or manages
                $projectIds = $user->projects()->pluck('projects.id');
                $projectQuery->whereIn('id', $projectIds);
            }

            $projects = $projectQuery->with(['capexEntries', 'opexEntries', 'bankGuarantees', 'invoices'])->get();
            
            $totalCapex = $projects->sum(fn($p) => $p->capexEntries->sum('amount'));
            $totalOpex = $projects->sum(fn($p) => $p->opexEntries->sum('amount'));
            $totalInvoiced = $projects->sum(fn($p) => $p->invoices->sum('total_amount'));
            
            $activeBGs = $projects->flatMap->bankGuarantees->where('status', 'active');
            $pendingInvoices = $projects->flatMap->invoices->where('status', 'pending');

            return [
                'total_projects' => $projects->count(),
                'active_bgs' => $activeBGs->count(),
                'pending_invoices' => $pendingInvoices->count(),
                'total_financials' => $totalCapex + $totalOpex,
                'total_invoiced' => $totalInvoiced,
                'budget_utilization' => $totalInvoiced > 0 ? round(($totalInvoiced / ($totalCapex + $totalOpex + 1)) * 100, 2) : 0,
                'expiring_bgs' => $activeBGs->filter(function($bg) {
                    return \Carbon\Carbon::parse($bg->validity_date)->diffInDays(now(), false) <= 30;
                })->count(),
            ];
        });
    }
}
