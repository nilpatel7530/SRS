@extends('adminlte::page')

@section('title', 'Financial Reconciliation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Financial Reconciliation Report</h1>
        <div>
            <button onclick="window.print()" class="btn btn-default"><i class="fas fa-print mr-1"></i> Print</button>
            <a href="{{ route('reports.export') }}" class="btn btn-success"><i class="fas fa-file-excel mr-1"></i> Export</a>
        </div>
    </div>
@stop

@section('content')
    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form action="{{ route('reports.reconciliation') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label class="mr-2">ISD:</label>
                    <select name="department_id" class="form-control form-control-sm">
                        <option value="">All ISDs</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ ($filters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label class="mr-2">From:</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="form-group mr-3">
                    <label class="mr-2">To:</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('reports.reconciliation') }}" class="btn btn-sm btn-default ml-2">Reset</a>
            </form>
        </div>
    </div>

    @foreach($reconciliationData as $project)
        <div class="card card-outline card-primary mb-4 shadow-sm">
            <div class="card-header py-2">
                <h3 class="card-title">
                    <span class="badge badge-info mr-2">{{ $project['customer'] }}</span>
                    <strong>{{ $project['project_name'] }}</strong>
                </h3>
                <div class="card-tools">
                    <span class="mr-3">Project Value: <strong class="text-primary">₹{{ number_format($project['project_value'], 2) }}</strong></span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover m-0" style="min-width: 1400px;">
                        <thead class="bg-light text-xs">
                            <tr>
                                <th colspan="3" class="text-center bg-gray-light border-bottom-0">Invoice / Work Order</th>
                                <th colspan="7" class="text-center bg-success-subtle border-bottom-0">Customer Section (Receipts & Deductions)</th>
                                <th colspan="8" class="text-center bg-warning-subtle border-bottom-0">Vendor Section (Payouts & Deductions)</th>
                                <th rowspan="2" class="align-middle text-right bg-info-subtle border-left">Net Margin</th>
                            </tr>
                            <tr>
                                <th style="width: 100px;">No</th>
                                <th style="width: 80px;">Date</th>
                                <th class="text-right" style="width: 100px;">Value</th>
                                
                                <th class="text-right" style="width: 90px;">Received</th>
                                <th style="width: 80px;">Date</th>
                                <th class="text-right text-muted" style="width: 70px;">TDS-IT</th>
                                <th class="text-right text-muted" style="width: 70px;">TDS-GST</th>
                                <th class="text-right text-muted" style="width: 70px;">LD</th>
                                <th class="text-right text-muted" style="width: 70px;">Other</th>
                                <th class="text-right font-weight-bold" style="width: 80px;">Total Ded.</th>

                                <th style="width: 120px;">Payment Note</th>
                                <th class="text-right text-muted" style="width: 60px;">TDS</th>
                                <th class="text-right text-muted" style="width: 60px;">GST-TDS</th>
                                <th class="text-right text-muted" style="width: 60px;">Bank</th>
                                <th class="text-right text-muted" style="width: 60px;">TA/DA</th>
                                <th class="text-right font-weight-bold" style="width: 80px;">Total Ded.</th>
                                <th class="text-right" style="width: 90px;">Paid</th>
                                <th style="width: 80px;">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($project['invoices'] as $invoice)
                                <tr>
                                    <td><small>{{ $invoice['invoice_no'] }} / {{ $invoice['vendor_invoice_no'] }}</small></td>
                                    <td class="text-nowrap text-xs">{{ $invoice['date'] }}</td>
                                    <td class="text-right">₹{{ number_format($invoice['value'], 2) }}</td>
                                    
                                    <td class="text-right text-success font-weight-bold">₹{{ number_format($invoice['received'], 2) }}</td>
                                    <td class="text-nowrap text-xs">{{ $invoice['received_date'] }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['cust_tds_it'], 0) }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['cust_tds_gst'], 0) }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['cust_ld'], 0) }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['cust_other'], 0) }}</td>
                                    <td class="text-right font-weight-bold text-xs">₹{{ number_format($invoice['cust_total_deductions'], 0) }}</td>

                                    <td class="text-xs">{{ $invoice['vendor_note'] }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['vend_tds'], 0) }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['vend_gst_tds'], 0) }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['vend_bank'], 0) }}</td>
                                    <td class="text-right text-muted text-xs">₹{{ number_format($invoice['vend_tada'], 0) }}</td>
                                    <td class="text-right font-weight-bold text-xs text-danger">₹{{ number_format($invoice['vend_total_deductions'], 0) }}</td>
                                    <td class="text-right text-primary font-weight-bold">₹{{ number_format($invoice['vendor_paid'], 2) }}</td>
                                    <td class="text-nowrap text-xs">{{ $invoice['vendor_date'] }}</td>
                                    
                                    <td class="text-right font-weight-bold bg-info-subtle border-left">₹{{ number_format($invoice['net_margin'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="text-center py-3 text-muted italic">No invoices recorded for this project.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($project['invoices']) > 0)
                            <tfoot class="bg-light font-weight-bold text-xs">
                                <tr>
                                    <td colspan="2" class="text-right">PROJECT TOTALS</td>
                                    <td class="text-right">₹{{ number_format(collect($project['invoices'])->sum('value'), 2) }}</td>
                                    <td class="text-right text-success">₹{{ number_format($project['total_received'], 2) }}</td>
                                    <td colspan="5"></td>
                                    <td class="text-right">₹{{ number_format(collect($project['invoices'])->sum('cust_total_deductions'), 0) }}</td>
                                    <td colspan="5"></td>
                                    <td class="text-right text-danger">₹{{ number_format(collect($project['invoices'])->sum('vend_total_deductions'), 0) }}</td>
                                    <td class="text-right text-primary">₹{{ number_format($project['total_vendor_paid'], 2) }}</td>
                                    <td></td>
                                    <td class="text-right bg-info-subtle border-left">₹{{ number_format($project['total_margin'], 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            <div class="card-footer py-2">
                <div class="row text-sm">
                    <div class="col-md-3"><strong>Margin Percentage:</strong> <span class="text-info">{{ $project['project_value'] > 0 ? round(($project['total_margin'] / $project['project_value']) * 100, 2) : 0 }}%</span></div>
                    <div class="col-md-3"><strong>Pending Recovery (Cust):</strong> <span class="text-danger">₹{{ number_format(collect($project['invoices'])->sum('value') - $project['total_received'], 2) }}</span></div>
                </div>
            </div>
        </div>
    @endforeach
@stop

@section('css')
    <style>
        .table td, .table th {
            padding: 0.5rem !important;
            vertical-align: middle !important;
        }
        .text-xs { font-size: 0.75rem; }
        .bg-gray-light { background-color: #f4f6f9; }
        .bg-success-subtle { background-color: rgba(40, 167, 69, 0.05) !important; }
        .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.05) !important; }
        .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.05) !important; }
        .bg-info-subtle { background-color: rgba(23, 162, 184, 0.05) !important; }
        @media print {
            .btn, .card-tools, .main-footer { display: none !important; }
            .card { border: 1px solid #ddd !important; margin-bottom: 20px !important; }
            .table-responsive { overflow: visible !important; }
        }
    </style>
@stop
