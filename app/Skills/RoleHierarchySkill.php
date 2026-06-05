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
    public function hasRole(User $user, string $roleName): bool
    {
        return $user->hasRole($roleName);
    }

    /**
     * Get users under a manager (Recursive).
     */
    public function getSubordinates(User $user): \Illuminate\Support\Collection
    {
        $subordinates = User::where('manager_id', $user->id)->get();
        $allSubordinates = collect($subordinates);

        foreach ($subordinates as $subordinate) {
            $allSubordinates = $allSubordinates->merge($this->getSubordinates($subordinate));
        }

        return $allSubordinates;
    }

    /**
     * Check if a user is managed by another user (Direct or Indirect).
     */
    public function isManagedBy(User $user, User $manager): bool
    {
        if ($user->manager_id === $manager->id) {
            return true;
        }

        if (!$user->manager_id) {
            return false;
        }

        $parent = User::find($user->manager_id);
        return $parent ? $this->isManagedBy($parent, $manager) : false;
    }
}
