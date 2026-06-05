<?php

namespace App\Skills;

use Modules\Projects\Models\Project;
use Modules\Projects\Models\ProjectActivity;
use Illuminate\Support\Facades\Auth;

class ProjectAuditSkill
{
    /**
     * Log a project activity.
     */
    public function logActivity(Project $project, string $type, string $description, array $properties = []): ProjectActivity
    {
        return ProjectActivity::create([
            'project_id' => $project->id,
            'user_id' => Auth::id() ?? 1, // Default to system user if no auth
            'type' => $type,
            'action' => 'created',
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
