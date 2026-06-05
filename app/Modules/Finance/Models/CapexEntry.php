<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Projects\Models\Project;

class CapexEntry extends Model
{
    protected $fillable = ['project_id', 'amount', 'remarks', 'completion_date', 'entry_date'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
