<?php

namespace App\Skills;

use Illuminate\Support\Facades\Cache;
use Modules\Projects\Models\Department;

class DropdownManagementSkill extends AbstractSkill
{
    protected string $name = 'Dropdown Management Skill';
    protected string $description = 'Handles dynamic, cached dropdowns for departments and types.';

    /**
     * Get departments for dropdown.
     */
    public function getDepartmentDropdown(): array
    {
        return Cache::remember('dropdown_departments', 3600, function () {
            return Department::pluck('name', 'id')->toArray();
        });
    }

    /**
     * Get project types for dropdown.
     */
    public function getProjectTypeDropdown(): array
    {
        return [
            'capex' => 'Capex',
            'opex' => 'Opex',
            'service' => 'Service',
            'supply' => 'Supply',
        ];
    }
}
