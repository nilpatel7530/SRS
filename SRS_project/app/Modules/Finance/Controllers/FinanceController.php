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
            'vendor_total' => 'required|numeric|min:0',
            'cel_total' => 'required|numeric|min:0',
            'payment_received' => 'nullable|numeric|min:0',
            'invoice_date' => 'required|date',
            'remarks' => 'nullable|string',
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
}
