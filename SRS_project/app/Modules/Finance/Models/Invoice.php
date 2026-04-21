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
        'work_description',
        'vendor_total',
        'vendor_gst',
        'vendor_total_with_gst',
        'cel_total',
        'cel_gst',
        'cel_total_with_gst',
        'payment_received',
        'customer_payment_date',
        'customer_payment_note',
        'vendor_payment_date',
        'vendor_paid_amount',
        'tds_deduction',
        'gst_tds_deduction',
        'bank_charges',
        'ta_da',
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
