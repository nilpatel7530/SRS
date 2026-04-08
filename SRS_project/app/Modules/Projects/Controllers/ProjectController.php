<?php

namespace Modules\Projects\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Projects\Models\Project;
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
        return view('projects.index', compact('projects'));
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

        return redirect()->back()->with('success', 'Project created successfully.');
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
