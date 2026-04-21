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
    @foreach($reconciliationData as $project)
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="badge badge-info mr-2">{{ $project['customer'] }}</span>
                    <strong>{{ $project['project_name'] }}</strong>
                </h3>
                <div class="card-tools">
                    <span class="mr-3">Project Value: <strong>₹{{ number_format($project['project_value'], 0) }}</strong></span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover m-0">
                        <thead class="bg-light text-xs">
                            <tr>
                                <th colspan="3" class="text-center bg-gray-light border-bottom-0">Invoice Details</th>
                                <th colspan="3" class="text-center bg-success-subtle border-bottom-0">Customer Payment</th>
                                <th colspan="5" class="text-center bg-warning-subtle border-bottom-0">Deductions</th>
                                <th colspan="2" class="text-center bg-danger-subtle border-bottom-0">Vendor Payout</th>
                                <th rowspan="2" class="align-middle text-right bg-info-subtle">Net Margin</th>
                            </tr>
                            <tr>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th class="text-right">Value</th>
                                <th>Received</th>
                                <th>Date</th>
                                <th>Note</th>
                                <th class="text-right">TDS</th>
                                <th class="text-right">GST-TDS</th>
                                <th class="text-right">Bank</th>
                                <th class="text-right">TA/DA</th>
                                <th class="text-right font-weight-bold">Total</th>
                                <th class="text-right">Paid</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project['invoices'] as $invoice)
                                <tr>
                                    <td>{{ $invoice['invoice_no'] }}</td>
                                    <td class="text-nowrap">{{ $invoice['date'] }}</td>
                                    <td class="text-right">₹{{ number_format($invoice['value'], 0) }}</td>
                                    <td class="text-right text-success font-weight-bold">₹{{ number_format($invoice['received'], 0) }}</td>
                                    <td class="text-nowrap">{{ $invoice['received_date'] }}</td>
                                    <td class="text-xs">{{ $invoice['received_note'] }}</td>
                                    <td class="text-right text-muted">₹{{ number_format($invoice['tds'], 0) }}</td>
                                    <td class="text-right text-muted">₹{{ number_format($invoice['gst_tds'], 0) }}</td>
                                    <td class="text-right text-muted">₹{{ number_format($invoice['bank_charges'], 0) }}</td>
                                    <td class="text-right text-muted">₹{{ number_format($invoice['ta_da'], 0) }}</td>
                                    <td class="text-right font-weight-bold text-danger">₹{{ number_format($invoice['total_deductions'], 0) }}</td>
                                    <td class="text-right text-primary font-weight-bold">₹{{ number_format($invoice['vendor_paid'], 0) }}</td>
                                    <td class="text-nowrap">{{ $invoice['vendor_date'] }}</td>
                                    <td class="text-right font-weight-bold bg-info-subtle">₹{{ number_format($invoice['net_margin'], 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-3 text-muted">No invoices recorded for this project.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($project['invoices']) > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="2" class="text-right">PROJECT TOTALS</td>
                                    <td class="text-right">₹{{ number_format(collect($project['invoices'])->sum('value'), 0) }}</td>
                                    <td class="text-right text-success">₹{{ number_format($project['total_received'], 0) }}</td>
                                    <td colspan="2"></td>
                                    <td colspan="4" class="text-right">Total Deductions:</td>
                                    <td class="text-right text-danger">₹{{ number_format(collect($project['invoices'])->sum('total_deductions'), 0) }}</td>
                                    <td class="text-right text-primary">₹{{ number_format($project['total_vendor_paid'], 0) }}</td>
                                    <td></td>
                                    <td class="text-right bg-info-subtle">₹{{ number_format($project['total_margin'], 0) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            <div class="card-footer py-2">
                <div class="row text-sm">
                    <div class="col-md-3"><strong>Margin Percentage:</strong> {{ $project['project_value'] > 0 ? round(($project['total_margin'] / $project['project_value']) * 100, 2) : 0 }}%</div>
                    <div class="col-md-3"><strong>Pending Recovery:</strong> ₹{{ number_format(collect($project['invoices'])->sum('value') - $project['total_received'], 0) }}</div>
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
