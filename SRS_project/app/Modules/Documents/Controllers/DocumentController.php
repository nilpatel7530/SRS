<?php

namespace Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Projects\Models\Project;
use Modules\Documents\Models\Document;
use App\Skills\DocumentManagementSkill;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    protected DocumentManagementSkill $documentSkill;
    protected \App\Skills\ProjectAuditSkill $auditSkill;

    public function __construct(DocumentManagementSkill $documentSkill, \App\Skills\ProjectAuditSkill $auditSkill)
    {
        $this->documentSkill = $documentSkill;
        $this->auditSkill = $auditSkill;
    }

    public function store(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        $request->validate([
            'files.*' => 'required|file|max:51200', // 50MB limit as per SRS
            'type' => 'required|string|max:50',
        ]);

        $documents = [];
        if ($request->hasFile('files')) {
            $documents = $this->documentSkill->uploadMultiple($project, $request->file('files'), $request->type);
            foreach ($documents as $doc) {
                $this->auditSkill->logActivity($project, 'document', "Uploaded document: {$doc->file_name} (Type: " . strtoupper($doc->type) . ")");
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully.',
                'data' => $documents
            ]);
        }

        return back()->with('success', 'Documents uploaded successfully.');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        // Authorization check could go here or in a Policy
        // For now, if user can see the project, they can download the file
        
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }
}
