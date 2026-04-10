<?php

namespace Modules\Reports\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Skills\ReportingEngineSkill;

class ReportController extends Controller
{
    protected ReportingEngineSkill $reportingSkill;

    public function __construct(ReportingEngineSkill $reportingSkill)
    {
        $this->reportingSkill = $reportingSkill;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['department_id', 'type']);
        $reportData = $this->reportingSkill->buildReportData($filters);

        return view('admin.reports.index', compact('reportData'));
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
