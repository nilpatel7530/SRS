@extends('adminlte::page')

@section('title', 'Project Details')

@section('content_header')
    <h1>Project: {{ $project->name }} <small>({{ strtoupper($project->type) }})</small></h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3"><i class="fas fa-cubes"></i> Project Information</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_details" data-toggle="tab">Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_finance" data-toggle="tab">Financials</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_documents" data-toggle="tab">Documents</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_bgs" data-toggle="tab">Bank Guarantees</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_invoices" data-toggle="tab">Invoices</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Details Tab -->
                        <div class="tab-pane active" id="tab_details">
                            <dl class="row">
                                <dt class="col-sm-3">Department</dt>
                                <dd class="col-sm-9">{{ $project->department->name ?? 'None' }}</dd>

                                <dt class="col-sm-3">Project Type</dt>
                                <dd class="col-sm-9">{{ ucfirst($project->type) }}</dd>
                                
                                <dt class="col-sm-3">Assigned Users</dt>
                                <dd class="col-sm-9">
                                    @forelse($project->users as $user)
                                        <span class="badge badge-info">{{ $user->name }}</span>
                                    @empty
                                        <span class="text-muted">No users assigned</span>
                                    @endforelse
                                </dd>
                            </dl>
                        </div>
                        
                        <!-- Finance Tab -->
                        <div class="tab-pane" id="tab_finance">
                            <h4>CAPEX Entries</h4>
                            <table class="table table-bordered mb-4">
                                <thead><tr><th>Date</th><th>Description</th><th>Amount</th></tr></thead>
                                <tbody>
                                    @forelse($project->capexEntries as $entry)
                                        <tr>
                                            <td>{{ $entry->entry_date }}</td>
                                            <td>{{ $entry->description }}</td>
                                            <td>{{ number_format($entry->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3">No CAPEX entries.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <h4>OPEX Entries</h4>
                            <table class="table table-bordered">
                                <thead><tr><th>Date</th><th>Description</th><th>Amount</th></tr></thead>
                                <tbody>
                                    @forelse($project->opexEntries as $entry)
                                        <tr>
                                            <td>{{ $entry->entry_date }}</td>
                                            <td>{{ $entry->description }}</td>
                                            <td>{{ number_format($entry->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3">No OPEX entries.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane" id="tab_documents">
                            <table class="table table-striped">
                                <thead><tr><th>Name</th><th>Type</th><th>Actions</th></tr></thead>
                                <tbody>
                                    @forelse($project->documents as $doc)
                                        <tr>
                                            <td>{{ $doc->file_name }}</td>
                                            <td><span class="badge badge-secondary">{{ $doc->type }}</span></td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">Download</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3">No documents attached.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Bank Guarantees Tab -->
                        <div class="tab-pane" id="tab_bgs">
                            <table class="table table-striped">
                                <thead><tr><th>Type</th><th>Amount</th><th>Validity Date</th><th>Status</th></tr></thead>
                                <tbody>
                                    @forelse($project->bankGuarantees as $bg)
                                        <tr>
                                            <td>{{ $bg->type }}</td>
                                            <td>{{ number_format($bg->amount, 2) }}</td>
                                            <td>{{ $bg->validity_date }}</td>
                                            <td><span class="badge {{ $bg->status == 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($bg->status) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4">No Bank Guarantees.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Invoices Tab -->
                        <div class="tab-pane" id="tab_invoices">
                            <table class="table table-striped">
                                <thead><tr><th>Vendor Inv No</th><th>Total Amount</th><th>Status</th><th>Date</th></tr></thead>
                                <tbody>
                                    @forelse($project->invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->vendor_invoice_no }}</td>
                                            <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                            <td><span class="badge badge-info">{{ ucfirst($invoice->status) }}</span></td>
                                            <td>{{ $invoice->invoice_date }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4">No Invoices.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
