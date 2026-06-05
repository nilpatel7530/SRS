<?php

namespace Modules\Projects\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTarget extends Model
{
    protected $fillable = [
        'project_id',
        'financial_year',
        'billed_prev_fy',
        'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar',
        'remarks'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the total target for the financial year.
     */
    public function getTotalTargetAttribute(): float
    {
        return $this->apr + $this->may + $this->jun + $this->jul + 
               $this->aug + $this->sep + $this->oct + $this->nov + 
               $this->dec + $this->jan + $this->feb + $this->mar;
    }
}
