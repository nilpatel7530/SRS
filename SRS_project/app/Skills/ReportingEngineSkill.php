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
    public function buildReportData(array $filters): array
    {
        $query = Project::with(['department', 'capexEntries', 'opexEntries', 'bankGuarantees', 'invoices']);

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->get()->map(function($project) {
            return [
                'project_name' => $project->name,
                'department' => $project->department->name ?? 'N/A',
                'total_capex' => $project->capexEntries->sum('amount'),
                'total_opex' => $project->opexEntries->sum('amount'),
                'bg_status' => $project->bankGuarantees->count() > 0 ? 'Active' : 'Missing',
            ];
        })->toArray();
    }
}
