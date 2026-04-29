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

    public function show(ProjectProposal $proposal)
    {
        $proposal->load('documents');
        return view('admin.proposals.edit', compact('proposal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'estimated_value' => 'nullable|numeric',
            'status' => 'required|in:Open,Closed,Awarded',
            'proposals.*' => 'nullable|mimes:pdf,doc,docx,xlsx,xls,zip,jpg,jpeg,png,svg|max:15360', // Increased to 15MB
        ]);

        $proposal = ProjectProposal::create([
            'name' => $request->name,
            'project_type' => $request->project_type,
            'state' => $request->state,
            'vendor_name' => $request->vendor_name,
            'work_order_date' => $request->work_order_date,
            'sent_by' => $request->sent_by,
            'status' => $request->status,
            'description_of_work' => $request->description_of_work,
            'estimated_value' => $request->estimated_value,
            'remarks' => $request->remarks,
            'created_by' => auth()->id(),
        ]);

        // Handle multi-proposal upload
        if ($request->hasFile('proposals')) {
            $files = $request->file('proposals');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $fileName = time() . '_' . str_replace(' ', '_', $originalName);
                        $path = $file->storeAs('proposals', $fileName, 'public');

                        \Modules\Documents\Models\Document::create([
                            'proposal_id' => $proposal->id,
                            'file_name' => $originalName,
                            'file_path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getClientOriginalExtension(),
                            'category' => 'Proposal',
                            'uploader_id' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('proposals.index')->with('success', 'Proposal Hub created successfully.');
    }

    public function edit(ProjectProposal $proposal)
    {
        $proposal->load('documents');
        return view('admin.proposals.edit', compact('proposal'));
    }

    public function update(Request $request, ProjectProposal $proposal)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'estimated_value' => 'nullable|numeric',
            'status' => 'required|in:Open,Closed,Awarded',
            'proposals.*' => 'nullable|mimes:pdf,doc,docx,xlsx,xls,zip,jpg,jpeg,png,svg|max:15360',
        ]);

        $proposal->update([
            'name' => $request->name,
            'project_type' => $request->project_type,
            'state' => $request->state,
            'vendor_name' => $request->vendor_name,
            'work_order_date' => $request->work_order_date,
            'sent_by' => $request->sent_by,
            'status' => $request->status,
            'description_of_work' => $request->description_of_work,
            'estimated_value' => $request->estimated_value,
            'remarks' => $request->remarks,
        ]);

        // Handle additional multi-proposal uploads
        if ($request->hasFile('proposals')) {
            $files = $request->file('proposals');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $fileName = time() . '_' . str_replace(' ', '_', $originalName);
                        $path = $file->storeAs('proposals', $fileName, 'public');

                        \Modules\Documents\Models\Document::create([
                            'proposal_id' => $proposal->id,
                            'file_name' => $originalName,
                            'file_path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getClientOriginalExtension(),
                            'category' => 'Proposal',
                            'uploader_id' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('proposals.index')->with('success', 'Proposal updated successfully.');
    }

    public function destroy(ProjectProposal $proposal)
    {
        $proposal->delete();
        return redirect()->route('proposals.index')->with('success', 'Proposal deleted successfully.');
    }
}
