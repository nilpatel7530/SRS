<?php

namespace App\Skills;

use Modules\Finance\Models\Invoice;

class InvoiceProcessingSkill extends AbstractSkill
{
    protected string $name = 'Invoice Processing Skill';
    protected string $description = 'Handles Vendor & CEL invoices, GST, and payment tracking.';

    /**
     * Create a new invoice with separate Vendor and CEL components.
     */
    public function createInvoice(\Modules\Projects\Models\Project $project, array $data): Invoice
    {
        // Vendor amounts
        $vendorTotal = $data['vendor_total'] ?? 0;
        $vendorGst = $data['vendor_gst'] ?? ($vendorTotal * 0.18);
        $vendorTotalWithGst = $data['vendor_total_with_gst'] ?? ($vendorTotal + $vendorGst);

        // CEL amounts
        $celTotal = $data['cel_total'] ?? 0;
        $celGst = $data['cel_gst'] ?? ($celTotal * 0.18);
        $celTotalWithGst = $data['cel_total_with_gst'] ?? ($celTotal + $celGst);

        return $project->invoices()->create([
            'vendor_invoice_no' => $data['vendor_invoice_no'] ?? null,
            'cel_invoice_no' => $data['cel_invoice_no'] ?? null,
            'vendor_total' => $vendorTotal,
            'vendor_gst' => $vendorGst,
            'vendor_total_with_gst' => $vendorTotalWithGst,
            'cel_total' => $celTotal,
            'cel_gst' => $celGst,
            'cel_total_with_gst' => $celTotalWithGst,
            'payment_received' => $data['payment_received'] ?? 0,
            'invoice_date' => $data['invoice_date'] ?? now(),
            'remarks' => $data['remarks'] ?? null,
            'status' => ($data['payment_received'] ?? 0) >= $celTotalWithGst ? 'paid' : 'pending',
            'total_amount' => $celTotalWithGst // Default total amount to CEL total with GST
        ]);
    }

    /**
     * Calculate values for a specific invoice amount.
     */
    public function calculateAmounts(float $baseAmount, float $gstPercentage = 18.0): array
    {
        $gstAmount = ($baseAmount * $gstPercentage) / 100;
        return [
            'base' => $baseAmount,
            'gst' => $gstAmount,
            'total' => $baseAmount + $gstAmount
        ];
    }

    /**
     * Group project invoices by month.
     */
    public function groupProjectInvoicesByMonth(\Modules\Projects\Models\Project $project): \Illuminate\Support\Collection
    {
        return $project->invoices()
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->groupBy(function($invoice) {
                return \Carbon\Carbon::parse($invoice->invoice_date)->format('F Y');
            });
    }
}
