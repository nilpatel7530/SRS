@extends('adminlte::page')

@section('title', 'Invoice Processing')

@section('content_header')
    <h1>Invoice Processing Hub</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Project Invoices</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover table-valign-middle">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Project</th>
                        <th class="text-right">Vendor Value</th>
                        <th class="text-right">CEL Value</th>
                        <th>Invoice Date</th>
                        <th class="text-center">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <strong>{{ $invoice->cel_invoice_no ?? 'N/A' }}</strong><br>
                                <small class="text-muted">Vendor: {{ $invoice->vendor_invoice_no }}</small>
                            </td>
                            <td>
                                <a href="{{ route('projects.show', $invoice->project_id) }}">
                                    {{ $invoice->project->name ?? 'Unknown Project' }}
                                </a>
                            </td>
                            <td class="text-right">{{ number_format($invoice->vendor_total, 0) }}</td>
                            <td class="text-right text-primary font-weight-bold">{{ number_format($invoice->cel_total, 0) }}</td>
                            <td>{{ date('d-m-Y', strtotime($invoice->invoice_date)) }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $invoice->status == 'released' ? 'success' : 'warning' }}">
                                    {{ ucfirst($invoice->status ?? 'Pending') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs btn-info" title="View Details"><i class="fas fa-eye"></i></button>
                                    @if($invoice->status != 'released')
                                        <button class="btn btn-xs btn-success" title="Approve & Release"><i class="fas fa-check"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-info-circle mr-2"></i> No invoices found in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
