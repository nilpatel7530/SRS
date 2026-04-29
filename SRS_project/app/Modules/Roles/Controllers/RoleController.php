<?php

namespace Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request, \App\Services\PermissionRegistryService $registry)
    {
        $registry->sync();
        if ($request->ajax()) {
            $roles = Role::withCount('permissions');
            return \Yajra\DataTables\DataTables::of($roles)
                ->addColumn('action', function($role) {
                    $actions = '<div class="btn-group">';
                    if (auth()->user()->can('roles.edit')) {
                        $actions .= '<a href="' . route('roles.edit', $role->id) . '" class="btn btn-info btn-xs"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('roles.delete') && $role->name !== 'Admin') {
                        $actions .= '<button type="button" class="btn btn-danger btn-xs delete-role" data-id="' . $role->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.roles.index');
    }

    public function create(\App\Services\PermissionRegistryService $registry)
    {
        $registry->sync();
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role, \App\Services\PermissionRegistryService $registry)
    {
        $registry->sync();
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('roles')->ignore($role->id)],
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Admin') {
            return response()->json(['success' => false, 'message' => 'Cannot delete the Admin role.'], 403);
        }
        
        $role->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
        }

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
