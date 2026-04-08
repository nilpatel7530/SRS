<?php

namespace App\Skills;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Projects\Models\Project;

class DocumentManagementSkill extends AbstractSkill
{
    protected string $name = 'Document Management Skill';
    protected string $description = 'Handles uploads, file categorization, and storage abstraction.';

    /**
     * Store a document for a project.
     */
    public function uploadDocument(Project $project, UploadedFile $file, string $type): string
    {
        $path = $file->store("projects/{$project->id}/{$type}", 'public');
        
        $project->documents()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'type' => $type,
        ]);

        return $path;
    }
}
