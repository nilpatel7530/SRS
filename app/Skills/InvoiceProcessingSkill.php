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
            'work_description' => $data['work_description'] ?? null,
            
            // Vendor amounts & payouts
            'vendor_total' => $vendorTotal,
            'vendor_gst' => $vendorGst,
            'vendor_total_with_gst' => $vendorTotalWithGst,
            'vendor_paid_amount' => $data['vendor_paid_amount'] ?? 0,
            'vendor_payment_date' => $data['vendor_payment_date'] ?? null,
            'vendor_payment_note' => $data['vendor_payment_note'] ?? null,
            
            // Vendor Deductions
            'tds_deduction' => $data['tds_deduction'] ?? 0,
            'gst_tds_deduction' => $data['gst_tds_deduction'] ?? 0,
            'bank_charges' => $data['bank_charges'] ?? 0,
            'ta_da' => $data['ta_da'] ?? 0,

            // CEL amounts & receipts
            'cel_total' => $celTotal,
            'cel_gst' => $celGst,
            'cel_total_with_gst' => $celTotalWithGst,
            'payment_received' => $data['payment_received'] ?? 0,
            'customer_payment_date' => $data['customer_payment_date'] ?? null,
            'customer_payment_note' => $data['customer_payment_note'] ?? null,
            
            // Customer Deductions
            'customer_tds_it' => $data['customer_tds_it'] ?? 0,
            'customer_tds_gst' => $data['customer_tds_gst'] ?? 0,
            'customer_ld' => $data['customer_ld'] ?? 0,
            'customer_any_other' => $data['customer_any_other'] ?? 0,

            'invoice_date' => $data['invoice_date'] ?? now(),
            'remarks' => $data['remarks'] ?? null,
            'status' => ($data['payment_received'] ?? 0) >= $celTotalWithGst ? 'paid' : 'pending',
            'total_amount' => $celTotalWithGst 
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
     * Update an existing invoice with a new payment amount.
     */
    public function updatePayment(Invoice $invoice, array $data): Invoice
    {
        if (isset($data['type']) && $data['type'] === 'vendor') {
            $newPayout = $data['payment_amount'] ?? 0;
            $invoice->update([
                'vendor_paid_amount' => $invoice->vendor_paid_amount + $newPayout,
                'vendor_payment_date' => $data['payment_date'] ?? now(),
                'vendor_payment_note' => $data['payment_note'] ?? null,
                'tds_deduction' => $data['tds_deduction'] ?? $invoice->tds_deduction,
                'gst_tds_deduction' => $data['gst_tds_deduction'] ?? $invoice->gst_tds_deduction,
                'bank_charges' => $data['bank_charges'] ?? $invoice->bank_charges,
                'ta_da' => $data['ta_da'] ?? $invoice->ta_da,
            ]);
        } else {
            $newPayment = $data['payment_amount'] ?? 0;
            $totalReceived = $invoice->payment_received + $newPayment;
            
            $invoice->update([
                'payment_received' => $totalReceived,
                'customer_payment_date' => $data['payment_date'] ?? now(),
                'customer_payment_note' => $data['payment_note'] ?? null,
                'customer_tds_it' => $data['customer_tds_it'] ?? $invoice->customer_tds_it,
                'customer_tds_gst' => $data['customer_tds_gst'] ?? $invoice->customer_tds_gst,
                'customer_ld' => $data['customer_ld'] ?? $invoice->customer_ld,
                'customer_any_other' => $data['customer_any_other'] ?? $invoice->customer_any_other,
                'status' => $totalReceived >= $invoice->cel_total_with_gst ? 'paid' : 'pending'
            ]);
        }

        return $invoice;
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
