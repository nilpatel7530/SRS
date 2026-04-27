<?php

namespace Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Projects\Models\Department;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'department'])->get();
        return view('admin.users.index', compact('users'));
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
            'department_id' => $request->department_id
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
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
