<?php

namespace Modules\Documents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Projects\Models\Project;

class Document extends Model
{
    protected $fillable = ['project_id', 'file_path', 'file_name', 'type'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
