<?php

namespace App\Skills;

use Modules\Projects\Models\Project;
use App\Models\User;

class ProjectManagementSkill extends AbstractSkill
{
    protected string $name = 'Project Management Skill';
    protected string $description = 'Handles project creation, assignment, and hierarchy mapping.';

    /**
     * Assign a user to a project.
     */
    public function assignUser(Project $project, User $user): void
    {
        $project->users()->attach($user->id);
    }

    /**
     * Map project hierarchy (Recursive or simple mapping).
     */
    public function getProjectHierarchy(Project $project): array
    {
        // Placeholder for advanced hierarchy logic
        return [
            'project' => $project->name,
            'department' => $project->department->name ?? 'None',
            'team_members' => $project->users->pluck('name')->toArray(),
        ];
    }
}
