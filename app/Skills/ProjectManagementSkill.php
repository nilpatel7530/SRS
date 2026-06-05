<?php

namespace App\Skills;

use Modules\Projects\Models\Project;
use App\Models\User;

class ProjectManagementSkill extends AbstractSkill
{
    protected string $name = 'Project Management Skill';
    protected string $description = 'Handles project creation, assignment, and hierarchy mapping.';

    /**
     * Create a new project.
     */
    public function createProject(array $data): Project
    {
        return Project::create($data);
    }

    /**
     * Assign a user to a project.
     */
    public function assignUser(Project $project, User $user): void
    {
        $project->users()->syncWithoutDetaching([$user->id]);
    }

    /**
     * Get the project portfolio accessible to a user.
     * Admin: all projects.
     * Manager/TL: their projects + projects of their subordinates.
     * User: only assigned projects.
     */
    public function getUserPortfolio(User $user, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Project::query()->with('department');

        if (!$user->isAdmin()) {
            // Get subordinates
            $hierarchySkill = app(RoleHierarchySkill::class);
            $subordinateIds = $hierarchySkill->getSubordinates($user)->pluck('id');
            
            // User's own assigned projects OR projects assigned to subordinates OR projects in their ISD
            $query->where(function ($q) use ($user, $subordinateIds) {
                $q->whereHas('users', function ($uq) use ($user) {
                    $uq->where('users.id', $user->id);
                })->orWhereHas('users', function ($uq) use ($subordinateIds) {
                    $uq->whereIn('users.id', $subordinateIds);
                })->orWhere('department_id', $user->department_id);
            });
        }

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['financial_type'])) {
            $query->where('financial_type', $filters['financial_type']);
        }

        if (!empty($filters['project_type'])) {
            $query->where('project_type', $filters['project_type']);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Map project hierarchy.
     */
    public function getProjectHierarchy(Project $project): array
    {
        return [
            'project' => $project->name,
            'department' => $project->department->name ?? 'None',
            'team_members' => $project->users()->with('roles')->get()->map(function($user) {
                return [
                    'name' => $user->name,
                    'role' => $user->getRoleNames()->first(),
                ];
            })->toArray(),
        ];
    }
}
