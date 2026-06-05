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
     * Store a document for a project or proposal (Private/Secure storage).
     */
    public function uploadDocument($model, UploadedFile $file, string $type, string $category = 'customer'): \Modules\Documents\Models\Document
    {
        // Determine storage path based on model type
        $modelType = ($model instanceof Project) ? 'projects' : 'proposals';
        
        // Store in private 'local' disk instead of 'public' for standard docs, 
        // but for proposals we might want public if the user specifically asked for preview links.
        // However, the skill should be consistent.
        $path = $file->store("{$modelType}/{$model->id}/{$type}", 'public');
        
        $data = [
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'type' => $type,
            'category' => $category,
            'size' => $file->getSize(),
            'uploader_id' => auth()->id(),
        ];

        if ($model instanceof Project) {
            $data['project_id'] = $model->id;
        } else {
            $data['proposal_id'] = $model->id;
        }

        return \Modules\Documents\Models\Document::create($data);
    }

    /**
     * Handle multi-file upload.
     */
    public function uploadMultiple($model, array $files, string $type, string $category = 'customer'): array
    {
        $uploaded = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploaded[] = $this->uploadDocument($project, $file, $type, $category);
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
