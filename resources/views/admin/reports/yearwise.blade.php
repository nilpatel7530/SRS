@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'YearWise Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>YearWise Dashboard ({{ $financialYear }})</h1>
        <div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                <i class="fas fa-calendar-alt mr-1"></i> Financial Year: {{ $financialYear }}
            </button>
            <div class="dropdown-menu shadow" role="menu">
                <a class="dropdown-item {{ $financialYear == '2025-26' ? 'active' : '' }}" href="{{ route('reports.yearwise', ['fy' => '2025-26']) }}">2025-26</a>
                <a class="dropdown-item {{ $financialYear == '2026-27' ? 'active' : '' }}" href="{{ route('reports.yearwise', ['fy' => '2026-27']) }}">2026-27</a>
                <a class="dropdown-item {{ $financialYear == '2027-28' ? 'active' : '' }}" href="{{ route('reports.yearwise', ['fy' => '2027-28']) }}">2027-28</a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">Ongoing Projects Targets & Billing</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover m-0 datatable" style="min-width: 1500px;">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2" class="align-middle sticky-col customer-col">Customers</th>
                            <th rowspan="2" class="align-middle sticky-col project-col">Project</th>
                            <th rowspan="2" class="align-middle text-center">Opex/Capex</th>
                            <th rowspan="2" class="align-middle text-right">Project Value (Excl. GST)</th>
                            <th rowspan="2" class="align-middle text-right">
                                FY {{ explode('-', $financialYear)[0]-1 }} Billed<br>
                                <small class="text-muted">(Target / Actual)</small>
                            </th>
                            <th colspan="12" class="text-center">
                                {{ $financialYear }} Monthly Billing<br>
                                <small class="text-muted">(Target / Actual*)</small>
                            </th>
                            <th rowspan="2" class="align-middle text-right">
                                Total Cover FY {{ explode('-', $financialYear)[1] }}<br>
                                <small class="text-muted">(Target / Actual)</small>
                            </th>
                            <th rowspan="2" class="align-middle">Remarks</th>
                        </tr>
                        <tr class="text-xs text-center">
                            <th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th><th>Sep</th>
                            <th>Oct</th><th>Nov</th><th>Dec</th><th>Jan</th><th>Feb</th><th>Mar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($targets as $index => $row)
                            <tr>
                                <td class="sticky-col customer-col">{{ $row['customer'] }}</td>
                                <td class="sticky-col project-col text-truncate" style="max-width: 200px;" title="{{ $row['project_name'] }}">
                                    <a href="{{ route('projects.show', $row['id']) }}">{{ $row['project_name'] }}</a>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $row['financial_type'] == 'CAPEX' ? 'badge-primary' : 'badge-warning' }}" style="font-size: 10px;">
                                        {{ $row['financial_type'] }}
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold">{{ number_format($row['project_value'], 0) }}</td>
                                <td class="text-right">
                                    <span class="text-muted">{{ number_format($row['billed_prev_fy'], 0) }}</span> / 
                                    <span class="text-info font-weight-bold">{{ number_format($row['billed_prev_fy_actual'], 0) }}</span>
                                </td>
                                
                                @foreach($row['targets'] as $month => $targetVal)
                                    @php 
                                        $actualVal = $row['actuals'][$month] ?? 0;
                                        $variance = $actualVal - $targetVal;
                                        $bgClass = '';
                                        if ($targetVal > 0) {
                                            if ($actualVal >= $targetVal) $bgClass = 'bg-success-subtle';
                                            elseif ($actualVal > 0) $bgClass = 'bg-warning-subtle';
                                            else $bgClass = 'bg-light';
                                        }
                                    @endphp
                                    <td class="text-right {{ $bgClass }} py-1" style="min-width: 90px;">
                                        <div class="text-xs text-muted mb-0" style="line-height: 1;">
                                            {{ $targetVal > 0 ? number_format($targetVal, 0) : '-' }}
                                        </div>
                                        <div class="font-weight-bold {{ $actualVal > 0 ? 'text-dark' : 'text-gray' }} border-top mt-1 pt-1" style="line-height: 1; border-top: 1px solid rgba(0,0,0,0.05) !important;">
                                            {{ $actualVal > 0 ? number_format($actualVal, 0) : '-' }}
                                        </div>
                                    </td>
                                @endforeach

                                <td class="text-right bg-info-light">
                                    <div class="text-xs">{{ number_format($row['total_target'], 0) }}</div>
                                    <div class="font-weight-bold text-primary">{{ number_format($row['total_actual'], 0) }}</div>
                                </td>
                                <td class="text-sm font-italic" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['remarks'] }}">
                                    {{ $row['remarks'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td class="sticky-col customer-col">TOTALS</td>
                            <td class="sticky-col project-col">{{ count($targets) }} Projects</td>
                            <td class="text-center">-</td>
                            <td class="text-right">{{ number_format(collect($targets)->sum('project_value'), 0) }}</td>
                            <td class="text-right">
                                {{ number_format(collect($targets)->sum('billed_prev_fy'), 0) }} /
                                <span class="text-info">{{ number_format(collect($targets)->sum('billed_prev_fy_actual'), 0) }}</span>
                            </td>
                            @foreach(['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $m)
                                <td class="text-right py-1">
                                    <div class="text-xs text-muted">{{ number_format(collect($targets)->sum("targets.$m"), 0) }}</div>
                                    <div>{{ number_format(collect($targets)->sum("actuals.$m"), 0) }}</div>
                                </td>
                            @endforeach
                            <td class="text-right text-primary">
                                <div class="text-xs">{{ number_format(collect($targets)->sum('total_target'), 0) }}</div>
                                <div>{{ number_format(collect($targets)->sum('total_actual'), 0) }}</div>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-left">
                <span class="badge bg-success-subtle border">Achieved</span>
                <span class="badge bg-warning-subtle border">Partial</span>
                <span class="badge bg-light border">No Actuals</span>
            </div>
            <div class="float-right">
                <small class="text-muted">* All values are in INR and exclude GST. Actuals are derived from approved invoices.</small>
            </div>
        </div>
    </div>
@stop

@include('partials.datatables-init', ['responsive' => false])
@section('css')
    <style>
        .table-responsive {
            overflow-x: auto;
            max-height: 80vh;
        }
        th.align-middle {
            vertical-align: middle !important;
        }
        .bg-success-subtle { background-color: rgba(40, 167, 69, 0.15) !important; }
        .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.15) !important; }
        .bg-info-light { background-color: rgba(23, 162, 184, 0.05); }
        
        .text-xs { font-size: 0.7rem; }
        .text-gray { color: #adb5bd; }
        
        /* Sticky Headers & Columns */
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f8f9fa;
            box-shadow: inset 0 -1px 0 #dee2e6;
        }
        
        .table thead tr:nth-child(2) th { top: 82px; }

        .table td, .table th {
            padding: 0.4rem 0.5rem !important;
            vertical-align: middle !important;
        }

        .table thead th {
            padding: 0.75rem 0.5rem !important;
        }

        .sticky-col {
            position: sticky;
            background: white !important;
            z-index: 5;
        }
        
        .customer-col { left: 0; min-width: 120px; }
        .project-col { 
            left: 120px; 
            min-width: 200px; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.05); 
        }
        
        thead .sticky-col { z-index: 11 !important; }
        tfoot .sticky-col { 
            z-index: 10 !important; 
            bottom: 0; 
            background: #f4f6f9 !important; 
        }
        
        tfoot tr td {
            position: sticky;
            bottom: 0;
            background: #f4f6f9;
            z-index: 9;
            box-shadow: inset 0 1px 0 #dee2e6;
        }
    </style>
@stop
