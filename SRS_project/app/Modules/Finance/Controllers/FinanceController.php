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

    public function __construct(
        FinancialTrackingSkill $financeSkill,
        InvoiceProcessingSkill $invoiceSkill,
        BankGuaranteeSkill $bgSkill
    ) {
        $this->financeSkill = $financeSkill;
        $this->invoiceSkill = $invoiceSkill;
        $this->bgSkill = $bgSkill;
    }

    public function storeCapex(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'remarks' => 'required|string',
            'completion_date' => 'required|date',
        ]);

        $this->financeSkill->addCapexEntry($project, $validated);

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

        $this->financeSkill->addOpexEntry($project, $validated);

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

        $this->invoiceSkill->createInvoice($project, $validated);

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

        $this->bgSkill->createBG($project, $validated);

        return back()->with('success', 'Bank Guarantee recorded successfully.');
    }
}
