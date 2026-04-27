<?php

namespace Modules\Reports\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Skills\ReportingEngineSkill;

class ReportController extends Controller
{
    protected ReportingEngineSkill $reportingSkill;
    protected \App\Skills\DashboardAggregationSkill $dashboardSkill;

    public function __construct(
        ReportingEngineSkill $reportingSkill,
        \App\Skills\DashboardAggregationSkill $dashboardSkill
    ) {
        $this->reportingSkill = $reportingSkill;
        $this->dashboardSkill = $dashboardSkill;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['department_id', 'type']);
        $reportData = $this->reportingSkill->buildReportData($filters);
        $overviewStats = $this->dashboardSkill->getKPIs($request->user());

        return view('admin.reports.index', compact('reportData', 'overviewStats'));
    }

    public function yearwise(Request $request)
    {
        $financialYear = $request->get('fy', '2026-27');
        $targets = $this->reportingSkill->getYearWiseTargets($financialYear);

        return view('admin.reports.yearwise', compact('targets', 'financialYear'));
    }

    public function reconciliation(Request $request)
    {
        $filters = $request->only(['department_id', 'start_date', 'end_date']);
        $reconciliationData = $this->reportingSkill->getFinancialReconciliationData($filters);
        $departments = \Modules\Projects\Models\Department::orderBy('name')->get();

        return view('admin.reports.reconciliation', compact('reconciliationData', 'departments', 'filters'));
    }

    public function exportCsv(Request $request)
    {
        $filters = $request->only(['department_id', 'type']);
        $reportData = $this->reportingSkill->buildReportData($filters);

        $fileName = 'project_report_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Project Name', 
            'Department', 
            'Type', 
            'Total CAPEX', 
            'Total OPEX', 
            'Total Invoiced', 
            'Pending Invoices', 
            'BG Count', 
            'Next BG Expiry', 
            'Status'
        ];

        $callback = function() use($reportData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row['project_name'],
                    $row['department'],
                    $row['type'],
                    $row['total_capex'],
                    $row['total_opex'],
                    $row['total_invoiced'],
                    $row['pending_invoices'],
                    $row['bg_count'],
                    $row['next_bg_expiry'],
                    $row['bg_status'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
