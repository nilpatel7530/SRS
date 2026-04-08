<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Projects\Models\Project;

class Invoice extends Model
{
    protected $fillable = [
        'project_id', 
        'vendor_invoice_no', 
        'cel_invoice_no', 
        'gst_amount', 
        'total_amount', 
        'status', 
        'invoice_date'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
