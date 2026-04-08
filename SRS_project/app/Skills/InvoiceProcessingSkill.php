<?php

namespace App\Skills;

use Modules\Finance\Models\Invoice;

class InvoiceProcessingSkill extends AbstractSkill
{
    protected string $name = 'Invoice Processing Skill';
    protected string $description = 'Handles Vendor & CEL invoices, GST, and payment tracking.';

    /**
     * Calculate total with GST.
     */
    public function calculateTotal(float $baseAmount, float $gstPercentage = 18.0): float
    {
        $gstAmount = ($baseAmount * $gstPercentage) / 100;
        return $baseAmount + $gstAmount;
    }

    /**
     * Group invoices by month.
     */
    public function groupInvoicesByMonth(array $invoices): array
    {
        $grouped = [];
        foreach ($invoices as $invoice) {
            $month = date('F Y', strtotime($invoice->invoice_date));
            $grouped[$month][] = $invoice;
        }
        return $grouped;
    }
}
