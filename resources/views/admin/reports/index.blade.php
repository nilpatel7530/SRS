@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'Reports')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>System Reports</h1>
        <div>
            <a href="{{ route('reports.reconciliation') }}" class="btn btn-primary mr-2">
                <i class="fas fa-balance-scale"></i> Reconciliation Report
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Overview Metrics (Relocated from Dashboard) -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $overviewStats['total_projects'] }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon"><i class="fas fa-project-diagram"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $overviewStats['budget_utilization'] }}%</h3>
                    <p>Budget Utilization</p>
                </div>
                <div class="icon"><i class="fas fa-chart-pie"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $overviewStats['active_bgs'] }}</h3>
                    <p>Active BGs</p>
                </div>
                <div class="icon"><i class="fas fa-shield-alt"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $overviewStats['pending_invoices'] }}</h3>
                    <p>Pending Invoices</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Consolidated Project Report (Landscape View)</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-bordered table-striped m-0 datatable">
                <thead class="bg-light">
                    <tr>
                        <th style="min-width: 200px;">Project Name</th>
                        <th class="text-center">Types</th>
                        <th class="text-right">Total CAPEX</th>
                        <th class="text-right">Total OPEX</th>
                        <th class="text-right">Invoiced</th>
                        @foreach(array_keys(current($reportData)['month_wise'] ?? []) as $month)
                            <th class="bg-light text-center" style="min-width: 80px;">{{ $month }}</th>
                        @endforeach
                        <th class="bg-info text-right">Projection</th>
                        <th class="text-center">BG Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportData as $row)
                        <tr>
                            <td>
                                <strong>{{ $row['project_name'] }}</strong><br>
                                <small class="text-muted">{{ $row['department'] }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $row['financial_type'] }}</span><br>
                                <span class="badge badge-outline-secondary">{{ $row['project_type'] }}</span>
                            </td>
                            <td class="text-right">{{ number_format($row['total_capex'], 0) }}</td>
                            <td class="text-right">{{ number_format($row['total_opex'], 0) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($row['total_invoiced'], 0) }}</td>
                            @foreach($row['month_wise'] as $total)
                                <td class="bg-light text-right">{{ $total > 0 ? number_format($total, 0) : '-' }}</td>
                            @endforeach
                            <td class="bg-info text-right font-weight-bold">{{ number_format(array_sum($row['projections']), 0) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $row['bg_status'] == 'Active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $row['bg_status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 7 + count(array_keys(current($reportData)['month_wise'] ?? [])) }}" class="text-center p-4">No projects found for the selected criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <style>
        .badge-outline-secondary {
            border: 1px solid #6c757d;
            color: #6c757d;
            background: transparent;
        }
    </style>
@stop

@include('partials.datatables-init')
