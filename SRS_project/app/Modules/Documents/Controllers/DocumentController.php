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

    public function __construct(DocumentManagementSkill $documentSkill)
    {
        $this->documentSkill = $documentSkill;
    }

    public function store(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        $request->validate([
            'files.*' => 'required|file|max:51200', // 50MB limit as per SRS
            'type' => 'required|string|max:50',
        ]);

        if ($request->hasFile('files')) {
            $this->documentSkill->uploadMultiple($project, $request->file('files'), $request->type);
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
