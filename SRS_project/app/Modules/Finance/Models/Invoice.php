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
        'vendor_total',
        'vendor_gst',
        'vendor_total_with_gst',
        'cel_total',
        'cel_gst',
        'cel_total_with_gst',
        'payment_received',
        'gst_amount', 
        'total_amount', 
        'status', 
        'invoice_date',
        'remarks'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
