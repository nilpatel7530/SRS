@extends('adminlte::page')

@section('title', 'Project Financial Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Dashboard Hub</h1>
        <div class="btn-group">
            <a href="{{ route('admin.dashboard', ['view' => 'yearwise', 'fy' => $fy]) }}" class="btn btn-{{ $view == 'yearwise' ? 'primary' : 'default' }}">
                <i class="fas fa-calendar-check mr-1"></i> YearWise Reports
            </a>
            <a href="{{ route('admin.dashboard', ['view' => 'dev']) }}" class="btn btn-{{ $view == 'dev' ? 'primary' : 'default' }}">
                <i class="fas fa-chart-line mr-1"></i> Development Reports
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- KPI Row -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['total_projects'] }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon"><i class="fas fa-project-diagram"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['invoice_count_fy'] }}</h3>
                    <p>Invoices (FY {{ $stats['fy_label'] ?? date('Y') }})</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['proposals_count'] }}</h3>
                    <p>Total Proposals</p>
                </div>
                <div class="icon"><i class="fas fa-file-contract"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $stats['expiring_bgs'] }}</h3>
                    <p>Expire BGs (30 Days)</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    @if($view == 'yearwise')
        <!-- YearWise Dashboard View -->
        <div class="card card-outline card-info">
            <div class="card-header border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-table mr-1"></i> Ongoing Projects Targets & Billing ({{ $fy }})
                    </h3>
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="fyDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-calendar-alt mr-1"></i> Financial Year: {{ $fy }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="{{ url()->current() }}?view={{ $view }}&fy=2025-26">2025-26</a>
                            <a class="dropdown-item" href="{{ url()->current() }}?view={{ $view }}&fy=2026-27">2026-27</a>
                            <a class="dropdown-item" href="{{ url()->current() }}?view={{ $view }}&fy=2027-28">2027-28</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px;">
                    <table class="table table-bordered table-striped table-hover m-0 table-sm datatable" style="min-width: 1400px;">
                        <thead class="bg-light sticky-top" style="z-index: 990;">
                            <tr class="text-center">
                                <th rowspan="2" class="align-middle">Customer</th>
                                <th rowspan="2" class="align-middle">Project Name</th>
                                <th rowspan="2" class="align-middle">Value</th>
                                <th rowspan="2" class="align-middle">Prev FY Actual</th>
                                <th colspan="12">Monthly Billing (Target / Actual)</th>
                                <th rowspan="2" class="align-middle">Total actual</th>
                            </tr>
                            <tr class="text-xs">
                                <th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th><th>Sep</th>
                                <th>Oct</th><th>Nov</th><th>Dec</th><th>Jan</th><th>Feb</th><th>Mar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yearWiseData as $row)
                                <tr>
                                    <td>{{ $row['customer'] }}</td>
                                    <td><a href="{{ route('projects.show', $row['id']) }}">{{ $row['project_name'] }}</a></td>
                                    <td class="text-right text-muted">{{ number_format($row['project_value'], 0) }}</td>
                                    <td class="text-right font-weight-bold text-info">{{ number_format($row['billed_prev_fy_actual'], 0) }}</td>
                                    
                                    @foreach(['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $m)
                                        <td class="text-right py-2 {{ $row['actuals'][$m] > 0 ? 'bg-success-light' : '' }}" style="min-width: 90px; vertical-align: middle;">
                                            <div class="text-xs text-muted mb-1" style="line-height: 1.2;">
                                                <small>Target:</small> {{ $row['targets'][$m] > 0 ? number_format($row['targets'][$m], 0) : '-' }}
                                            </div>
                                            <div class="font-weight-bold pt-1" style="line-height: 1.2; border-top: 1px solid rgba(0,0,0,0.1) !important;">
                                                <small>Actual:</small> {{ $row['actuals'][$m] > 0 ? number_format($row['actuals'][$m], 0) : '-' }}
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="text-right font-weight-bold text-primary">{{ number_format($row['total_actual'], 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Actuals are captured from approved and released invoices.</small>
            </div>
        </div>
    @else
        <!-- Development Reports View -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="{{ route('projects.create') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-plus-circle mr-2"></i> Create New Project
                            </a>
                            <a href="{{ route('proposals.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-contract mr-2"></i> View Recent Proposals
                            </a>
                            <a href="{{ route('finance.invoices.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-money-bill-wave mr-2"></i> Process Invoices
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">System Insights</h3>
                    </div>
                    <div class="card-body">
                        <p>Portfolio Health: <strong class="text-{{ $stats['portfolio_health']['color'] }}">{{ $stats['portfolio_health']['text'] }}</strong></p>
                        <p>Recent Activity: <strong>{{ $stats['recent_cleared_count'] }}</strong> invoices cleared in the last 7 days.</p>
                        <a href="{{ route('reports.index') }}" class="btn btn-block btn-outline-secondary">Go to Full Analytics</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('plugins.Datatables', true)

@section('css')
    <style>
        .small-box { border-radius: 10px; overflow: hidden; }
        .bg-success-light { background-color: rgba(40, 167, 69, 0.05); }
        .sticky-top { top: 0; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,0.05); }
        .table-responsive { border-radius: 4px; border: 1px solid #dee2e6; }
        th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
@stop

@push('js')
    @include('partials.datatables-init', ['responsive' => false])
@endpush
