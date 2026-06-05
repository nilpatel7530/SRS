<?php

namespace Modules\Documents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Projects\Models\Project;

class Document extends Model
{
    protected $fillable = ['project_id', 'proposal_id', 'file_path', 'file_name', 'type', 'category', 'size', 'uploader_id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(\Modules\Projects\Models\ProjectProposal::class, 'proposal_id');
    }
}
