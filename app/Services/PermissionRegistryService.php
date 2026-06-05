<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Config;

class PermissionRegistryService
{
    /**
     * Scan sidebar and register new permissions.
     */
    public function sync()
    {
        $permissions = $this->collectPermissions(Config::get('adminlte.menu', []));
        
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }

    /**
     * Recursively collect 'can' attributes from menu array.
     */
    private function collectPermissions(array $menu): array
    {
        $permissions = [];
        
        foreach ($menu as $item) {
            if (isset($item['can'])) {
                if (is_array($item['can'])) {
                    $permissions = array_merge($permissions, $item['can']);
                } else {
                    $permissions[] = $item['can'];
                }
            }
            
            if (isset($item['submenu'])) {
                $permissions = array_merge($permissions, $this->collectPermissions($item['submenu']));
            }
        }
        
        return array_unique($permissions);
    }
}
