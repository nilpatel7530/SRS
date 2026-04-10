<?php

namespace Modules\Projects\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Projects\Models\ProjectProposal;

class ProposalController extends Controller
{
    public function index()
    {
        $proposals = ProjectProposal::with('creator')->latest()->get();
        return view('admin.proposals.index', compact('proposals'));
    }

    public function create()
    {
        return view('admin.proposals.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'estimated_value' => 'nullable|numeric',
            'submission_date' => 'nullable|date',
        ]);

        ProjectProposal::create([
            'name' => $request->name,
            'status' => 'Pending',
            'submission_date' => $request->submission_date,
            'estimated_value' => $request->estimated_value,
            'remarks' => $request->remarks,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('proposals.index')->with('success', 'Proposal created successfully.');
    }

    public function edit(ProjectProposal $proposal)
    {
        return view('admin.proposals.edit', compact('proposal'));
    }

    public function update(Request $request, ProjectProposal $proposal)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'estimated_value' => 'nullable|numeric',
            'submission_date' => 'nullable|date',
        ]);

        $proposal->update($request->all());

        return redirect()->route('proposals.index')->with('success', 'Proposal updated successfully.');
    }

    public function destroy(ProjectProposal $proposal)
    {
        $proposal->delete();
        return redirect()->route('proposals.index')->with('success', 'Proposal deleted successfully.');
    }
}
