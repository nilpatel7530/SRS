<?php

namespace Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Projects\Models\Department;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with(['roles', 'department'])->select('users.*');
            return DataTables::of($users)
                ->addColumn('roles_list', function($user) {
                    return $user->roles->pluck('name')->map(function($role) {
                        return '<span class="badge badge-info">'.$role.'</span>';
                    })->implode(' ');
                })
                ->addColumn('status_label', function($user) {
                    $class = $user->is_active ? 'success' : 'danger';
                    $text = $user->is_active ? 'Active' : 'Inactive';
                    return '<span class="badge badge-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('actions', function($user) {
                    $btns = '';
                    if (auth()->user()->can('users.edit')) {
                        $btns .= '<a href="'.route('users.edit', $user->id).'" class="btn btn-sm btn-warning mr-1"><i class="fas fa-edit"></i></a>';
                        $statusBtnClass = $user->is_active ? 'secondary' : 'success';
                        $statusBtnText = $user->is_active ? 'Deactivate' : 'Activate';
                        $btns .= '<button type="button" class="btn btn-sm btn-'.$statusBtnClass.' toggle-status" data-id="'.$user->id.'">'.$statusBtnText.'</button> ';
                    }
                    if (auth()->user()->can('users.delete') && $user->id !== auth()->id()) {
                        $btns .= '<button type="button" class="btn btn-sm btn-danger delete-user" data-id="'.$user->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    return $btns;
                })
                ->rawColumns(['roles_list', 'status_label', 'actions'])
                ->make(true);
        }
        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        $managers = User::with('roles')->get();
        $departments = Department::all();
        return view('admin.users.create', compact('roles', 'managers', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'roles' => 'array',
            'manager_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'manager_id' => $request->manager_id,
            'department_id' => $request->department_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->has('roles')) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        $managers = User::where('id', '!=', $user->id)->with('roles')->get();
        $departments = Department::all();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'managers', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'roles' => 'array',
            'manager_id' => 'nullable|exists:users,id|different:id',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'manager_id' => $request->manager_id,
            'department_id' => $request->department_id,
            'is_active' => $request->boolean('is_active', false),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete yourself.'], 403);
            }
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        }
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot deactivate yourself.'], 403);
            }
            return redirect()->route('users.index')->with('error', 'You cannot deactivate yourself.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => "User has been $status successfully.", 'is_active' => $user->is_active]);
        }
        return redirect()->route('users.index')->with('success', "User has been $status successfully.");
    }
}
