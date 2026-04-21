<?php

namespace Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Skills\DashboardAggregationSkill;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardAggregationSkill $dashboardSkill;
    protected \App\Skills\ReportingEngineSkill $reportingSkill;

    public function __construct(
        DashboardAggregationSkill $dashboardSkill,
        \App\Skills\ReportingEngineSkill $reportingSkill
    ) {
        $this->dashboardSkill = $dashboardSkill;
        $this->reportingSkill = $reportingSkill;
    }

    public function index(Request $request)
    {
        $view = $request->get('view', 'yearwise');
        $fy = $request->get('fy', '2026-27');
        
        $stats = $this->dashboardSkill->getKPIs($request->user());
        $yearWiseData = $this->reportingSkill->getYearWiseTargets($fy);

        return view('admin.dashboard', compact('stats', 'yearWiseData', 'view', 'fy'));
    }
}
