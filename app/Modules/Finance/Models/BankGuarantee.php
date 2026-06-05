<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Projects\Models\Project;

class BankGuarantee extends Model
{
    protected $fillable = ['project_id', 'amount', 'type', 'validity_date', 'status', 'bg_no', 'bg_date'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
