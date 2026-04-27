<?php

namespace Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Projects\Models\Project;
use App\Skills\FinancialTrackingSkill;
use App\Skills\InvoiceProcessingSkill;
use App\Skills\BankGuaranteeSkill;

class FinanceController extends Controller
{
    protected FinancialTrackingSkill $financeSkill;
    protected InvoiceProcessingSkill $invoiceSkill;
    protected BankGuaranteeSkill $bgSkill;
    protected \App\Skills\ProjectAuditSkill $auditSkill;

    public function __construct(
        FinancialTrackingSkill $financeSkill,
        InvoiceProcessingSkill $invoiceSkill,
        BankGuaranteeSkill $bgSkill,
        \App\Skills\ProjectAuditSkill $auditSkill
    ) {
        $this->financeSkill = $financeSkill;
        $this->invoiceSkill = $invoiceSkill;
        $this->bgSkill = $bgSkill;
        $this->auditSkill = $auditSkill;
    }

    public function index()
    {
        $invoices = \Modules\Finance\Models\Invoice::with('project')->latest()->get();
        return view('admin.finance.invoices.index', compact('invoices'));
    }

    public function storeCapex(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'remarks' => 'required|string',
            'completion_date' => 'required|date',
        ]);

        $entry = $this->financeSkill->addCapexEntry($project, $validated);
        $this->auditSkill->logActivity($project, 'capex', "Added CAPEX entry of amount {$validated['amount']} with remarks: {$validated['remarks']}");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'CAPEX entry added successfully.',
                'data' => $entry
            ]);
        }

        return back()->with('success', 'CAPEX entry added successfully.');
    }

    public function storeOpex(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'remarks' => 'required|string',
            'duration' => 'required|string',
            'entry_date' => 'required|date',
        ]);

        $entry = $this->financeSkill->addOpexEntry($project, $validated);
        $this->auditSkill->logActivity($project, 'opex', "Added OPEX entry of amount {$validated['amount']} for duration {$validated['duration']}");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'OPEX entry added successfully.',
                'data' => $entry
            ]);
        }

        return back()->with('success', 'OPEX entry added successfully.');
    }

    public function storeInvoice(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'vendor_invoice_no' => 'required|string',
            'cel_invoice_no' => 'nullable|string',
            'work_description' => 'nullable|string',
            'vendor_total' => 'required|numeric|min:0',
            'cel_total' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'remarks' => 'nullable|string',
            
            // Customer Side
            'payment_received' => 'nullable|numeric|min:0',
            'customer_payment_date' => 'nullable|date',
            'customer_payment_note' => 'nullable|string',
            'customer_tds_it' => 'nullable|numeric|min:0',
            'customer_tds_gst' => 'nullable|numeric|min:0',
            'customer_ld' => 'nullable|numeric|min:0',
            'customer_any_other' => 'nullable|numeric|min:0',
            
            // Vendor Side
            'vendor_paid_amount' => 'nullable|numeric|min:0',
            'vendor_payment_date' => 'nullable|date',
            'vendor_payment_note' => 'nullable|string',
            'tds_deduction' => 'nullable|numeric|min:0',
            'gst_tds_deduction' => 'nullable|numeric|min:0',
            'bank_charges' => 'nullable|numeric|min:0',
            'ta_da' => 'nullable|numeric|min:0',
        ]);

        $invoice = $this->invoiceSkill->createInvoice($project, $validated);
        $this->auditSkill->logActivity($project, 'invoice', "Recorded Invoice: {$validated['vendor_invoice_no']} (Vendor) / " . ($validated['cel_invoice_no'] ?? 'N/A') . " (CEL)");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice recorded successfully.',
                'data' => $invoice
            ]);
        }

        return back()->with('success', 'Invoice recorded successfully.');
    }

    public function updatePayment(Request $request, $projectId, $invoiceId)
    {
        $project = Project::findOrFail($projectId);
        $invoice = \Modules\Finance\Models\Invoice::findOrFail($invoiceId);
        
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_note' => 'nullable|string',
            'type' => 'required|in:customer,vendor',
            
            // Customer Deductions
            'customer_tds_it' => 'nullable|numeric|min:0',
            'customer_tds_gst' => 'nullable|numeric|min:0',
            'customer_ld' => 'nullable|numeric|min:0',
            'customer_any_other' => 'nullable|numeric|min:0',
            
            // Vendor Deductions
            'tds_deduction' => 'nullable|numeric|min:0',
            'gst_tds_deduction' => 'nullable|numeric|min:0',
            'bank_charges' => 'nullable|numeric|min:0',
            'ta_da' => 'nullable|numeric|min:0',
        ]);

        $invoice = $this->invoiceSkill->updatePayment($invoice, $validated);
        $typeLabel = $validated['type'] === 'vendor' ? 'Vendor Payout' : 'Customer Receipt';
        $this->auditSkill->logActivity($project, 'invoice', "Recorded {$typeLabel}: {$validated['payment_amount']} for Invoice: {$invoice->vendor_invoice_no}. Status: " . strtoupper($invoice->status));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'data' => $invoice
            ]);
        }

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function storeBG(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'bg_no' => 'required|string',
            'bg_date' => 'required|date',
            'type' => 'required|in:ABG,PBG,AMC-BG',
            'amount' => 'required|numeric|min:0',
            'validity_date' => 'required|date',
        ]);

        $bg = $this->bgSkill->createBG($project, $validated);
        $this->auditSkill->logActivity($project, 'bg', "Recorded {$validated['type']} Bank Guarantee: {$validated['bg_no']} for amount {$validated['amount']}");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bank Guarantee recorded successfully.',
                'data' => $bg
            ]);
        }

        return back()->with('success', 'Bank Guarantee recorded successfully.');
    }

    public function storeTarget(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'financial_year' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'billed_prev_fy' => 'nullable|numeric|min:0',
            'apr' => 'nullable|numeric|min:0',
            'may' => 'nullable|numeric|min:0',
            'jun' => 'nullable|numeric|min:0',
            'jul' => 'nullable|numeric|min:0',
            'aug' => 'nullable|numeric|min:0',
            'sep' => 'nullable|numeric|min:0',
            'oct' => 'nullable|numeric|min:0',
            'nov' => 'nullable|numeric|min:0',
            'dec' => 'nullable|numeric|min:0',
            'jan' => 'nullable|numeric|min:0',
            'feb' => 'nullable|numeric|min:0',
            'mar' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $target = $this->financeSkill->updateProjectTarget($project, $validated);
        $this->auditSkill->logActivity($project, 'target', "Updated targets for FY {$validated['financial_year']}");

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Targets updated successfully.',
                'data' => array_merge($target->toArray(), ['total_target' => $target->total_target])
            ]);
        }

        return back()->with('success', 'Targets updated successfully.');
    }
}
