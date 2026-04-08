<?php

namespace App\Skills;

use Illuminate\Support\Facades\Cache;
use Modules\Roles\Models\Role;
use App\Models\User;

class RoleHierarchySkill extends AbstractSkill
{
    protected string $name = 'Role Hierarchy Skill';
    protected string $description = 'Handles RBAC, role CRUD, and hierarchy (Manager -> TL -> User).';

    /**
     * Check if a user has a specific role.
     */
    public function hasRole(User $user, string $roleSlug): bool
    {
        return $user->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Get users under a manager (Simplified).
     */
    public function getSubordinates(User $user): array
    {
        // Simple hierarchy check - can be expanded to real employee/manager relation
        return [];
    }
}
