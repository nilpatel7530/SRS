@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'Financial Reconciliation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Financial Reconciliation Report</h1>
        <div>
            <button onclick="window.print()" class="btn btn-default"><i class="fas fa-print mr-1"></i> Print</button>
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

    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover m-0 datatable" style="min-width: 2000px;">
                    <thead class="bg-light text-xs text-center align-middle">
                        <tr>
                            <th rowspan="2" style="width: 150px; vertical-align: middle;">Project Name</th>
                            <th rowspan="2" style="width: 50px; vertical-align: middle;">S.No.</th>
                            <th colspan="10" class="bg-success-subtle border-bottom-0">CEL</th>
                            <th colspan="7" class="bg-warning-subtle border-bottom-0">Vendor</th>
                        </tr>
                        <tr>
                            <!-- CEL Columns -->
                            <th style="width: 100px;">Invoice No.</th>
                            <th style="width: 90px;">Invoice Date</th>
                            <th style="width: 100px;">Invoice amount</th>
                            <th style="width: 100px;">Payment Received amount</th>
                            <th style="width: 90px;">Payment Date</th>
                            <th style="width: 80px;">IT-TDS</th>
                            <th style="width: 80px;">GST-TDS</th>
                            <th style="width: 100px;">Other (Bank charges/LD/TA-DA)</th>
                            <th style="width: 100px;">Total Payment</th>
                            <th style="width: 120px;">Remarks</th>

                            <!-- Vendor Columns -->
                            <th style="width: 100px;">Invoice No.</th>
                            <th style="width: 90px;">Invoice Date</th>
                            <th style="width: 100px;">Invoice amount</th>
                            <th style="width: 100px;">Payment Note Date</th>
                            <th style="width: 90px;">Payment Voucher Date</th>
                            <th style="width: 100px;">Amount Released</th>
                            <th style="width: 120px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @php $globalSerial = 1; @endphp
                        @foreach($reconciliationData as $project)
                            @if(count($project['invoices']) > 0)
                                @foreach($project['invoices'] as $index => $invoice)
                                    <tr>
                                        @if($index === 0)
                                            <td class="align-middle border-bottom-0 pb-0">
                                                <strong>{{ $project['project_name'] }}</strong>
                                                <br><small class="text-muted">{{ $project['customer'] }}</small>
                                            </td>
                                        @else
                                            <td class="align-middle border-top-0 pt-0 text-muted">
                                                <small>{{ $project['project_name'] }}</small>
                                            </td>
                                        @endif
                                        <td class="text-center">{{ $globalSerial++ }}</td>
                                        
                                        <!-- CEL -->
                                        <td><small>{{ $invoice['invoice_no'] }}</small></td>
                                        <td class="text-nowrap text-xs">{{ $invoice['date'] }}</td>
                                        <td class="text-right">₹{{ number_format($invoice['value'], 2) }}</td>
                                        <td class="text-right text-success font-weight-bold">₹{{ number_format($invoice['received'], 2) }}</td>
                                        <td class="text-nowrap text-xs">{{ $invoice['received_date'] }}</td>
                                        <td class="text-right text-muted text-xs">₹{{ number_format($invoice['cust_tds_it'], 2) }}</td>
                                        <td class="text-right text-muted text-xs">₹{{ number_format($invoice['cust_tds_gst'], 2) }}</td>
                                        <td class="text-right text-muted text-xs">₹{{ number_format($invoice['cust_ld'] + $invoice['cust_other'], 2) }}</td>
                                        <td class="text-right font-weight-bold text-xs">₹{{ number_format($invoice['cel_total_payment'], 2) }}</td>
                                        <td class="text-xs">{{ $invoice['remarks'] }}</td>
                                        
                                        <!-- Vendor -->
                                        <td><small>{{ $invoice['vendor_invoice_no'] }}</small></td>
                                        <td class="text-nowrap text-xs">{{ $invoice['date'] }}</td>
                                        <td class="text-right">₹{{ number_format($invoice['vendor_amount'], 2) }}</td>
                                        <td class="text-xs">{{ $invoice['vendor_note'] }}</td>
                                        <td class="text-nowrap text-xs">{{ $invoice['vendor_date'] }}</td>
                                        <td class="text-right text-primary font-weight-bold">₹{{ number_format($invoice['vendor_paid'], 2) }}</td>
                                        <td class="text-xs">{{ $invoice['remarks'] }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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

@include('partials.datatables-init', ['responsive' => false])
