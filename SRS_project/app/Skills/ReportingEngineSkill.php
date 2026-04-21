<?php

namespace App\Skills;

use Modules\Projects\Models\Project;

class ReportingEngineSkill extends AbstractSkill
{
    protected string $name = 'Reporting Engine Skill';
    protected string $description = 'Handles dynamic filters, consolidated data building, and exports.';

    /**
     * Build reporting data based on filters.
     */
    public function buildReportData(array $filters = []): array
    {
        $query = Project::with(['department', 'capexEntries', 'opexEntries', 'bankGuarantees', 'invoices']);

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['financial_type'])) {
            $query->where('financial_type', $filters['financial_type']);
        }
        
        if (!empty($filters['project_type'])) {
            $query->where('project_type', $filters['project_type']);
        }

        return $query->get()->map(function($project) {
            $bg = $project->bankGuarantees->sortBy('validity_date')->first();
            
            // Month-wise Invoice calculation (Last 6 months)
            $monthWiseInvoices = [];
            for ($i = 0; $i < 6; $i++) {
                $monthStr = now()->subMonths($i)->format('Y-m');
                $monthWiseInvoices[$monthStr] = $project->invoices()
                    ->where('invoice_date', 'like', $monthStr . '%')
                    ->sum('total_amount');
            }

            // Simple Sales Projection for next 3 months (based on Opex burn rate or fixed projection)
            $recentAvg = $project->invoices()->where('invoice_date', '>=', now()->subMonths(3)->startOfMonth())->sum('total_amount') / 3;
            $projections = [
                now()->addMonth()->format('Y-m') => $recentAvg * 1.1, // 10% expected growth
                now()->addMonths(2)->format('Y-m') => $recentAvg * 1.15,
                now()->addMonths(3)->format('Y-m') => $recentAvg * 1.2,
            ];

            return [
                'project_name' => $project->name,
                'department' => $project->department->name ?? 'N/A',
                'financial_type' => strtoupper($project->financial_type ?? 'N/A'),
                'project_type' => strtoupper($project->project_type ?? 'N/A'),
                'total_capex' => $project->capexEntries->sum('amount'),
                'total_opex' => $project->opexEntries->sum('amount'),
                'total_invoiced' => $project->invoices->sum('total_amount'),
                'pending_invoices' => $project->invoices->where('status', 'pending')->sum('total_amount'),
                'bg_count' => $project->bankGuarantees->count(),
                'next_bg_expiry' => $bg ? $bg->validity_date : 'N/A',
                'bg_status' => $bg ? ucfirst($bg->status) : 'Missing',
                'month_wise' => $monthWiseInvoices,
                'projections' => $projections,
            ];
        })->toArray();
    }

    /**
     * Get Year-Wise targets and actual billing for projects.
     */
    public function getYearWiseTargets(string $financialYear = '2026-27'): array
    {
        // Parse FY (e.g. 2026-27)
        [$startYear, $endYearAbbr] = explode('-', $financialYear);
        $startYearInt = (int)$startYear;
        $endYearInt = 2000 + (int)$endYearAbbr;

        $startDate = "$startYearInt-04-01";
        $endDate = "$endYearInt-03-31";

        // Previous FY Range
        $prevStart = ($startYearInt - 1) . "-04-01";
        $prevEnd = ($startYearInt - 1) . "-03-31"; // This is actually end of march of current start year
        // Wait, prev FY for 2026-27 is 2025-04-01 to 2026-03-31
        $prevEnd = $startYearInt . "-03-31";

        return Project::with([
            'department', 
            'projectTargets' => function($query) use ($financialYear) {
                $query->where('financial_year', $financialYear);
            },
            'invoices' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('invoice_date', [$startDate, $endDate]);
            }
        ])->get()->map(function($project) use ($startYearInt) {
            $target = $project->projectTargets->first();
            
            // Map actual invoices to months
            $actuals = [
                'apr' => 0, 'may' => 0, 'jun' => 0, 'jul' => 0, 'aug' => 0, 'sep' => 0,
                'oct' => 0, 'nov' => 0, 'dec' => 0, 'jan' => 0, 'feb' => 0, 'mar' => 0
            ];

            foreach ($project->invoices as $invoice) {
                $m = strtolower(date('M', strtotime($invoice->invoice_date)));
                if (isset($actuals[$m])) {
                    $actuals[$m] += $invoice->total_amount;
                }
            }

            // Actual billing in previous FY
            $billedPrevFYActual = $project->invoices()
                ->where('invoice_date', '<', "$startYearInt-04-01")
                ->sum('total_amount');

            $targetsData = [
                'apr' => $target ? $target->apr : 0,
                'may' => $target ? $target->may : 0,
                'jun' => $target ? $target->jun : 0,
                'jul' => $target ? $target->jul : 0,
                'aug' => $target ? $target->aug : 0,
                'sep' => $target ? $target->sep : 0,
                'oct' => $target ? $target->oct : 0,
                'nov' => $target ? $target->nov : 0,
                'dec' => $target ? $target->dec : 0,
                'jan' => $target ? $target->jan : 0,
                'feb' => $target ? $target->feb : 0,
                'mar' => $target ? $target->mar : 0,
            ];

            return [
                'id' => $project->id,
                'customer' => $project->department->name ?? 'N/A',
                'project_name' => $project->name,
                'financial_type' => strtoupper($project->financial_type ?? 'N/A'),
                'project_value' => $project->capexEntries->sum('amount') + $project->opexEntries->sum('amount'),
                'billed_prev_fy' => $target ? $target->billed_prev_fy : 0,
                'billed_prev_fy_actual' => $billedPrevFYActual,
                'targets' => $targetsData,
                'actuals' => $actuals,
                'total_target' => $target ? $target->total_target : 0,
                'total_actual' => array_sum($actuals),
                'remarks' => $target ? $target->remarks : '',
            ];
        })->toArray();
    }
    /**
     * Get data for Financial Reconciliation report.
     */
    public function getFinancialReconciliationData(): array
    {
        return Project::with(['department', 'invoices' => function($query) {
            $query->orderBy('invoice_date', 'asc');
        }])->get()->map(function($project) {
            $invoicesData = $project->invoices->map(function($invoice) {
                $deductions = ($invoice->tds_deduction ?? 0) + 
                              ($invoice->gst_tds_deduction ?? 0) + 
                              ($invoice->bank_charges ?? 0) + 
                              ($invoice->ta_da ?? 0);
                
                return [
                    'id' => $invoice->id,
                    'invoice_no' => $invoice->cel_invoice_no,
                    'date' => $invoice->invoice_date,
                    'desc' => $invoice->work_description,
                    'value' => $invoice->total_amount,
                    'received' => $invoice->payment_received,
                    'received_date' => $invoice->customer_payment_date,
                    'received_note' => $invoice->customer_payment_note,
                    'tds' => $invoice->tds_deduction ?? 0,
                    'gst_tds' => $invoice->gst_tds_deduction ?? 0,
                    'bank_charges' => $invoice->bank_charges ?? 0,
                    'ta_da' => $invoice->ta_da ?? 0,
                    'total_deductions' => $deductions,
                    'vendor_name' => $invoice->vendor_name ?? 'N/A',
                    'vendor_paid' => $invoice->vendor_paid_amount ?? 0,
                    'vendor_date' => $invoice->vendor_payment_date,
                    'net_margin' => ($invoice->payment_received ?? 0) - ($invoice->vendor_paid_amount ?? 0) - $deductions
                ];
            });

            return [
                'id' => $project->id,
                'customer' => $project->department->name ?? 'N/A',
                'project_name' => $project->name,
                'project_value' => $project->capexEntries->sum('amount') + $project->opexEntries->sum('amount'),
                'invoices' => $invoicesData,
                'total_received' => $project->invoices->sum('payment_received'),
                'total_vendor_paid' => $project->invoices->sum('vendor_paid_amount'),
                'total_margin' => $invoicesData->sum('net_margin')
            ];
        })->toArray();
    }
}
