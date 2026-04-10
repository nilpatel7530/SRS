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

    public function __construct(ProjectManagementSkill $projectSkill)
    {
        $this->projectSkill = $projectSkill;
    }

    /**
     * Display a listing of projects based on user portfolio.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['department_id', 'type']);
        $projects = $this->projectSkill->getUserPortfolio($request->user(), $filters);
        
        return view('admin.projects.index', compact('projects'));
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
            'users'
        ])->findOrFail($id);

        $hierarchy = $this->projectSkill->getProjectHierarchy($project);

        return view('admin.projects.show', compact('project', 'hierarchy'));
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

        return back()->with('success', 'Team member assigned successfully.');
    }
}
