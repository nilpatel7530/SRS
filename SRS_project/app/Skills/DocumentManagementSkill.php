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
     * Store a document for a project (Private/Secure storage).
     */
    public function uploadDocument(Project $project, UploadedFile $file, string $type): \Modules\Documents\Models\Document
    {
        // Store in private 'local' disk instead of 'public'
        $path = $file->store("projects/{$project->id}/{$type}", 'local');
        
        return $project->documents()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'type' => $type,
        ]);
    }

    /**
     * Handle multi-file upload.
     */
    public function uploadMultiple(Project $project, array $files, string $type): array
    {
        $uploaded = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploaded[] = $this->uploadDocument($project, $file, $type);
            }
        }
        return $uploaded;
    }

    /**
     * Get secure download link (temporary/signed if using S3, or proxy if local).
     */
    public function getDownloadUrl(\Modules\Documents\Models\Document $document): string
    {
        // Placeholder for secure URL generation
        return route('documents.download', $document->id);
    }
}
