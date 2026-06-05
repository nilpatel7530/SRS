<?php

namespace Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index(Request $request, \App\Services\PermissionRegistryService $registry)
    {
        $registry->sync();
        if ($request->ajax()) {
            $query = Permission::query();
            
            if ($request->module) {
                $query->where('name', 'like', $request->module . '.%');
            }
            
            if ($request->action_filter) {
                $query->where('name', 'like', '%.' . $request->action_filter);
            }

            return \Yajra\DataTables\DataTables::of($query)
                ->addColumn('module', function($permission) {
                    $parts = explode('.', $permission->name);
                    return ucfirst($parts[0] ?? 'Global');
                })
                ->addColumn('action_name', function($permission) {
                    $parts = explode('.', $permission->name);
                    return ucfirst($parts[1] ?? $permission->name);
                })
                ->addColumn('action', function($permission) {
                    $actions = '<div class="btn-group">';
                    if (auth()->user()->can('permissions.edit')) {
                        $actions .= '<a href="' . route('permissions.edit', $permission->id) . '" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('permissions.delete')) {
                        $actions .= '<button type="button" class="btn btn-danger btn-xs delete-permission" data-id="' . $permission->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $allPermissions = Permission::all();
        $modules = $allPermissions->map(function($p) {
            return explode('.', $p->name)[0];
        })->unique()->filter()->values();
        
        $actions = $allPermissions->map(function($p) {
            return explode('.', $p->name)[1] ?? null;
        })->unique()->filter()->values();

        return view('admin.permissions.index', compact('modules', 'actions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($permission->id),
            ],
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Permission deleted successfully.']);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
