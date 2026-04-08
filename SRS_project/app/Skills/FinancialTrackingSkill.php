<?php

namespace App\Skills;

use Modules\Projects\Models\Project;

class FinancialTrackingSkill extends AbstractSkill
{
    protected string $name = 'Financial Tracking Skill';
    protected string $description = 'Handles Capex / Opex logic, aggregation and projections.';

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
        
        return [
            'total_capex' => $capex,
            'total_opex' => $opex,
            'grand_total' => $capex + $opex,
            'projections' => ($capex + $opex) * 1.1, // Example dummy projection
        ];
    }
}
