<?php

namespace Modules\Projects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class ProjectProposal extends Model
{
    protected $fillable = [
        'name',
        'project_type',
        'state',
        'description_of_work',
        'vendor_name',
        'work_order_date',
        'sent_by',
        'status',
        'submission_date',
        'estimated_value',
        'remarks',
        'created_by'
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(\Modules\Documents\Models\Document::class, 'proposal_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
