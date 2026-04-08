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
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::with('department')->get();
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
            'type' => 'required|in:capex,opex,service,supply',
        ]);

        $project = Project::create($validated);

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

        return view('admin.projects.show', compact('project'));
    }
}
