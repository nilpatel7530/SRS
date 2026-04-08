<?php

namespace Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Skills\DashboardAggregationSkill;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected DashboardAggregationSkill $dashboardSkill;

    public function __construct(DashboardAggregationSkill $dashboardSkill)
    {
        $this->dashboardSkill = $dashboardSkill;
    }

    public function index(Request $request)
    {
        $stats = $this->dashboardSkill->getKPIs($request->user());

        return view('admin.dashboard', compact('stats'));
    }
}
