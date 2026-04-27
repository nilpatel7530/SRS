<?php

namespace Modules\Projects\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Projects\Models\Project;
use Modules\Projects\Models\Department;
use App\Skills\ProjectManagementSkill;

class ProjectController extends Controller
{
    protected ProjectManagementSkill $projectSkill;
    protected \App\Skills\ProjectAuditSkill $auditSkill;

    public function __construct(ProjectManagementSkill $projectSkill, \App\Skills\ProjectAuditSkill $auditSkill)
    {
        $this->projectSkill = $projectSkill;
        $this->auditSkill = $auditSkill;
    }

    /**
     * Display a listing of projects based on user portfolio.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'department_id', 'financial_type', 'project_type']);
        $projects = $this->projectSkill->getUserPortfolio($request->user(), $filters);
        $departments = Department::all();
        
        return view('admin.projects.index', compact('projects', 'departments'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $departments = Department::all();
        return view('admin.projects.create', compact('departments'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'financial_type' => 'required|in:capex,opex',
            'project_type' => 'required|in:service,supply',
            'is_extension' => 'nullable|boolean',
            'extension_details' => 'nullable|string',
        ]);

        $project = $this->projectSkill->createProject($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project in a tabbed user interface.
     */
    public function show($id)
    {
        $project = Project::with([
            'department', 
            'capexEntries', 
            'opexEntries', 
            'bankGuarantees', 
            'invoices', 
            'documents',
            'users',
            'projectTargets'
        ])->findOrFail($id);

        $financeSkill = app(\App\Skills\FinancialTrackingSkill::class);
        $targetActuals = [];
        foreach ($project->projectTargets as $target) {
            $targetActuals[$target->financial_year] = $financeSkill->getMonthlyActuals($project, $target->financial_year);
        }

        $hierarchy = $this->projectSkill->getProjectHierarchy($project);
        $logs = \Modules\Projects\Models\ProjectActivity::with('user')
            ->where('project_id', $project->id)
            ->latest()
            ->get();

        return view('admin.projects.show', compact('project', 'hierarchy', 'logs', 'targetActuals'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $departments = Department::all();
        return view('admin.projects.edit', compact('project', 'departments'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'financial_type' => 'required|in:capex,opex',
            'project_type' => 'required|in:service,supply',
            'is_extension' => 'nullable|boolean',
            'extension_details' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    /**
     * Assign a user to the project.
     */
    public function assignTeam(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $this->projectSkill->assignUser($project, $user);
        
        $roleName = $user->roles->first()->name ?? 'N/A';
        $this->auditSkill->logActivity($project, 'team', "Assigned team member: {$user->name} ($roleName)");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Team member assigned successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name ?? 'N/A'
                ]
            ]);
        }

        return back()->with('success', 'Team member assigned successfully.');
    }
}
