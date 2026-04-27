@extends('adminlte::page')

@section('title', 'Project Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Project: {{ $project->name }} <small>({{ strtoupper($project->financial_type) }} / {{ strtoupper($project->project_type) }})</small></h1>
        <div class="btn-group">
            <a href="{{ route('projects.index') }}" class="btn btn-default btn-sm">Back to List</a>
            @can('projects.edit')
                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm">Edit</a>
            @endcan
            @can('projects.delete')
                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Really delete this project?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            @endcan
        </div>
    </div>
@stop

@section('content')
    @php
        $totalCapex = $project->capexEntries->sum('amount');
        $totalBilling = $project->invoices->sum('cel_total');
        $totalReceived = $project->invoices->sum('payment_received');
        $outstanding = $totalBilling - $totalReceived;
    @endphp

    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-hammer"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total CAPEX</span>
                    <span class="info-box-number" id="widget-capex">{{ number_format($totalCapex, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Billing</span>
                    <span class="info-box-number" id="widget-billing">{{ number_format($totalBilling, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-hand-holding-usd"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Received</span>
                    <span class="info-box-number" id="widget-received">{{ number_format($totalReceived, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Outstanding</span>
                    <span class="info-box-number" id="widget-outstanding">{{ number_format($outstanding, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Custom Tabs -->
            <div class="card">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3"><i class="fas fa-cubes"></i> Project Information</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#tab_details" data-toggle="tab">Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_finance" data-toggle="tab">Financials</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_documents" data-toggle="tab">Documents (C/V)</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_bgs" data-toggle="tab">Bank Guarantees</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_invoices" data-toggle="tab">Invoices (C/V)</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_targets" data-toggle="tab">Financial Targets</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_team" data-toggle="tab">Team</a></li>
                        @can('administration.access')
                            <li class="nav-item"><a class="nav-link text-danger" href="#tab_logs" data-toggle="tab"><i class="fas fa-history"></i> Activity Logs</a></li>
                        @endcan
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Details Tab -->
                        <div class="tab-pane active" id="tab_details">
                            <div class="row">
                                <div class="@can('administration.access') col-md-7 @else col-md-12 @endcan">
                                    <h5 class="mb-4 text-primary border-bottom pb-2"><i class="fas fa-info-circle"></i> Basic Metadata</h5>
                                    <div class="row mb-3">
                                        <div class="col-sm-5 text-muted font-weight-bold"><i class="fas fa-building fa-fw mr-1"></i> ISD</div>
                                        <div class="col-sm-7">{{ $project->department->name ?? 'None' }}</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-5 text-muted font-weight-bold"><i class="fas fa-coins fa-fw mr-1"></i> Financial Type</div>
                                        <div class="col-sm-7"><span class="badge badge-info">{{ strtoupper($project->financial_type) }}</span></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-5 text-muted font-weight-bold"><i class="fas fa-project-diagram fa-fw mr-1"></i> Project Type</div>
                                        <div class="col-sm-7"><span class="badge badge-secondary">{{ ucfirst($project->project_type) }}</span></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-5 text-muted font-weight-bold"><i class="fas fa-users fa-fw mr-1"></i> Active Team</div>
                                        <div class="col-sm-7" id="details-team-list">
                                            @forelse($project->users as $user)
                                                <div class="mb-1">
                                                    <i class="fas fa-user-circle text-muted"></i> {{ $user->name }}
                                                </div>
                                            @empty
                                                <span class="text-muted">No users assigned</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                
                                @can('administration.access')
                                <div class="col-md-5">
                                    <h5 class="mb-4 text-primary border-bottom pb-2"><i class="fas fa-history"></i> Recent Activity</h5>
                                    <div class="timeline timeline-inverse" id="details-timeline">
                                        @forelse($logs->take(5) as $log)
                                            <div>
                                                <i class="fas fa-check-circle bg-secondary"></i>
                                                <div class="timeline-item shadow-none border" style="margin-left: 40px; margin-right: 0;">
                                                    <span class="time"><i class="far fa-clock"></i> {{ $log->created_at->diffForHumans() }}</span>
                                                    <h3 class="timeline-header" style="font-size: 13px;"><b>{{ $log->user->name }}</b> {{ $log->description }}</h3>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted text-center pt-3">No activity recorded yet.</p>
                                        @endforelse
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="#tab_logs" data-toggle="tab" class="btn btn-xs btn-outline-primary">View Full Audit Log</a>
                                    </div>
                                </div>
                                @endcan
                            </div>
                        </div>
                        
                        <!-- Finance Tab -->
                        <div class="tab-pane" id="tab_finance">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>CAPEX Entries</h4>
                                    <table class="table table-bordered mb-4">
                                        <thead><tr><th>Date</th><th>Description</th><th>Amount</th></tr></thead>
                                        <tbody id="capex-table-body">
                                            @forelse($project->capexEntries as $entry)
                                                <tr>
                                                    <td>{{ $entry->completion_date ?? $entry->entry_date }}</td>
                                                    <td>{{ $entry->remarks }}</td>
                                                    <td>{{ number_format($entry->amount, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3">No CAPEX entries.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <h4>OPEX Entries</h4>
                                    <table class="table table-bordered">
                                        <thead><tr><th>Date</th><th>Description</th><th>Duration</th><th>Amount</th></tr></thead>
                                        <tbody id="opex-table-body">
                                            @forelse($project->opexEntries as $entry)
                                                <tr>
                                                    <td>{{ $entry->entry_date }}</td>
                                                    <td>{{ $entry->remarks }}</td>
                                                    <td>{{ $entry->duration }}</td>
                                                    <td>{{ number_format($entry->amount, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3">No OPEX entries.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-4 border-left">
                                    <h5>Add CAPEX Entry</h5>
                                    <form action="{{ route('projects.storeCapex', $project->id) }}" method="POST" class="mb-4 ajax-form" id="capex-form">
                                        @csrf
                                        <div class="form-group"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                                        <div class="form-group"><input type="text" name="remarks" class="form-control" placeholder="Remarks" required></div>
                                        <div class="form-group"><label>Completion Date</label><input type="date" name="completion_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                                        <button type="submit" class="btn btn-sm btn-primary">Add CAPEX</button>
                                    </form>

                                    <hr>

                                    <h5>Add OPEX Entry</h5>
                                    <form action="{{ route('projects.storeOpex', $project->id) }}" method="POST" class="ajax-form" id="opex-form">
                                        @csrf
                                        <div class="form-group"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                                        <div class="form-group"><input type="text" name="remarks" class="form-control" placeholder="Remarks" required></div>
                                        <div class="form-group"><input type="text" name="duration" class="form-control" placeholder="Duration (e.g. 12 months)" required></div>
                                        <div class="form-group"><input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                                        <button type="submit" class="btn btn-sm btn-info">Add OPEX</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane" id="tab_documents">
                            <div class="row">
                                <div class="col-md-9">
                                    <h5 class="text-primary"><i class="fas fa-user-tie"></i> Customer Documents</h5>
                                    <table class="table table-sm table-striped mb-4">
                                        <thead><tr><th>Name</th><th>Type</th><th>Actions</th></tr></thead>
                                        <tbody id="documents-customer-table-body">
                                            @forelse($project->documents->where('category', 'customer') as $doc)
                                                <tr>
                                                    <td>{{ $doc->file_name }}</td>
                                                    <td><span class="badge badge-secondary">{{ strtoupper($doc->type) }}</span></td>
                                                    <td><a href="{{ route('documents.download', $doc->id) }}" class="btn btn-xs btn-outline-primary"><i class="fas fa-download"></i> Download</a></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-muted">No customer documents.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <h5 class="text-warning"><i class="fas fa-truck-loading"></i> Vendor Documents</h5>
                                    <table class="table table-sm table-striped">
                                        <thead><tr><th>Name</th><th>Type</th><th>Actions</th></tr></thead>
                                        <tbody id="documents-vendor-table-body">
                                            @forelse($project->documents->where('category', 'vendor') as $doc)
                                                <tr>
                                                    <td>{{ $doc->file_name }}</td>
                                                    <td><span class="badge badge-secondary">{{ strtoupper($doc->type) }}</span></td>
                                                    <td><a href="{{ route('documents.download', $doc->id) }}" class="btn btn-xs btn-outline-primary"><i class="fas fa-download"></i> Download</a></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-muted">No vendor documents.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 border-left">
                                    <h5>Upload Documents</h5>
                                    <form action="{{ route('documents.store', $project->id) }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="documents-form">
                                        @csrf
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="form-control form-control-sm" required>
                                                <option value="customer">Customer</option>
                                                <option value="vendor">Vendor</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Document Name</label>
                                            <select name="type" class="form-control form-control-sm" id="document-type-select" required>
                                                <option value="Purchase Order">Purchase Order</option>
                                                <option value="BG">Bank Guarantee</option>
                                                <option value="Agreement">Agreement</option>
                                                <option value="Work Order">Work Order</option>
                                                <option value="other">Other (Specify below)</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="custom-type-group" style="display:none;">
                                            <label>Specify Other Type</label>
                                            <input type="text" name="custom_type" class="form-control form-control-sm" placeholder="Enter document type...">
                                        </div>
                                        <div class="form-group">
                                            <label>Select Files</label>
                                            <input type="file" name="files[]" class="form-control-file" multiple required>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary btn-block">Upload Files</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Guarantees Tab -->
                        <div class="tab-pane" id="tab_bgs">
                            <div class="row">
                                <div class="col-md-9">
                                    <table class="table table-striped">
                                        <thead><tr><th>BG No</th><th>BG Date</th><th>Type</th><th>Amount</th><th>Validity</th><th>Status</th></tr></thead>
                                        <tbody id="bg-table-body">
                                            @forelse($project->bankGuarantees as $bg)
                                                <tr>
                                                    <td>{{ $bg->bg_no ?? 'N/A' }}</td>
                                                    <td>{{ $bg->bg_date ?? 'N/A' }}</td>
                                                    <td>{{ $bg->type }}</td>
                                                    <td>{{ number_format($bg->amount, 2) }}</td>
                                                    <td>{{ $bg->validity_date }}</td>
                                                    <td><span class="badge {{ $bg->status == 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($bg->status) }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6">No Bank Guarantees.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 border-left">
                                    <h5>Issue New BG</h5>
                                    <form action="{{ route('projects.storeBG', $project->id) }}" method="POST" class="ajax-form" id="bg-form">
                                        @csrf
                                        <div class="form-group"><input type="text" name="bg_no" class="form-control" placeholder="BG Number" required></div>
                                        <div class="form-group"><label>BG Date</label><input type="date" name="bg_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                                        <div class="form-group">
                                            <select name="type" class="form-control" required>
                                                <option value="ABG">Advance Bank Guarantee (ABG)</option>
                                                <option value="PBG">Performance Bank Guarantee (PBG)</option>
                                                <option value="AMC-BG">AMC BG</option>
                                            </select>
                                        </div>
                                        <div class="form-group"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                                        <div class="form-group"><label>Validity Date</label><input type="date" name="validity_date" class="form-control" required></div>
                                        <button type="submit" class="btn btn-sm btn-success btn-block">Record BG</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Invoices Tab -->
                        <div class="tab-pane" id="tab_invoices">
                            <div class="row">
                                <div class="col-md-9">
                                    <h5 class="text-primary font-weight-bold"><i class="fas fa-file-invoice-dollar"></i> Customer Invoices (CEL)</h5>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-sm table-striped table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Inv No</th>
                                                    <th>Date</th>
                                                    <th>Total (with GST)</th>
                                                    <th>Received</th>
                                                    <th>TDS (IT/GST)</th>
                                                    <th>LD / Other</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoices-customer-table-body">
                                                @forelse($project->invoices as $invoice)
                                                    <tr data-invoice-id="{{ $invoice->id }}">
                                                        <td>{{ $invoice->cel_invoice_no ?? '-' }}</td>
                                                        <td>{{ $invoice->invoice_date }}</td>
                                                        <td class="text-right">{{ number_format($invoice->cel_total_with_gst, 2) }}</td>
                                                        <td class="text-right text-success font-weight-bold">{{ number_format($invoice->payment_received, 2) }}</td>
                                                        <td class="text-right text-muted" style="font-size: 11px;">
                                                            {{ number_format($invoice->customer_tds_it, 2) }} / {{ number_format($invoice->customer_tds_gst, 2) }}
                                                        </td>
                                                        <td class="text-right text-muted" style="font-size: 11px;">
                                                            {{ number_format($invoice->customer_ld, 2) }} / {{ number_format($invoice->customer_any_other, 2) }}
                                                        </td>
                                                        <td class="text-center"><span class="badge badge-status {{ $invoice->status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($invoice->status) }}</span></td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-xs btn-outline-success btn-record-payment" 
                                                                    data-id="{{ $invoice->id }}" 
                                                                    data-type="customer"
                                                                    data-no="{{ $invoice->cel_invoice_no }}"
                                                                    data-received="{{ $invoice->payment_received }}"
                                                                    data-total="{{ $invoice->cel_total_with_gst }}"
                                                                    title="Record Receipt">
                                                                <i class="fas fa-plus-circle"></i> Receipt
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="8" class="text-center text-muted">No customer invoices.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <h5 class="text-orange font-weight-bold"><i class="fas fa-hand-holding-usd"></i> Vendor Payouts</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Inv No</th>
                                                    <th>Work Description</th>
                                                    <th>Total</th>
                                                    <th>Paid</th>
                                                    <th>Deductions (TDS/GST)</th>
                                                    <th>Bank/TA/DA</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoices-vendor-table-body">
                                                @forelse($project->invoices as $invoice)
                                                    <tr data-invoice-id="{{ $invoice->id }}">
                                                        <td>{{ $invoice->vendor_invoice_no }}</td>
                                                        <td><small>{{ $invoice->work_description ?? '-' }}</small></td>
                                                        <td class="text-right">{{ number_format($invoice->vendor_total_with_gst, 2) }}</td>
                                                        <td class="text-right text-primary font-weight-bold">{{ number_format($invoice->vendor_paid_amount, 2) }}</td>
                                                        <td class="text-right text-muted" style="font-size: 11px;">
                                                            {{ number_format($invoice->tds_deduction, 2) }} / {{ number_format($invoice->gst_tds_deduction, 2) }}
                                                        </td>
                                                        <td class="text-right text-muted" style="font-size: 11px;">
                                                            {{ number_format($invoice->bank_charges, 2) }} / {{ number_format($invoice->ta_da, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-xs btn-outline-primary btn-record-payment" 
                                                                    data-id="{{ $invoice->id }}" 
                                                                    data-type="vendor"
                                                                    data-no="{{ $invoice->vendor_invoice_no }}"
                                                                    data-received="{{ $invoice->vendor_paid_amount }}"
                                                                    data-total="{{ $invoice->vendor_total_with_gst }}"
                                                                    title="Record Payout">
                                                                <i class="fas fa-paper-plane"></i> Payout
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="7" class="text-center text-muted">No vendor invoices.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-3 border-left">
                                    <h5>Record New Invoice</h5>
                                    <form action="{{ route('projects.storeInvoice', $project->id) }}" method="POST" class="ajax-form" id="invoices-form">
                                        @csrf
                                        <div class="form-group">
                                            <label>Work Description</label>
                                            <input type="text" name="work_description" class="form-control form-control-sm" placeholder="Short description of work done" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><div class="form-group"><label>Vendor Inv #</label><input type="text" name="vendor_invoice_no" class="form-control form-control-sm" required></div></div>
                                            <div class="col-6"><div class="form-group"><label>CEL Inv #</label><input type="text" name="cel_invoice_no" class="form-control form-control-sm"></div></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><div class="form-group"><label>Vendor Base</label><input type="number" step="0.01" name="vendor_total" class="form-control form-control-sm" required></div></div>
                                            <div class="col-6"><div class="form-group"><label>CEL Base</label><input type="number" step="0.01" name="cel_total" class="form-control form-control-sm" required></div></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Invoice Date</label>
                                            <input type="date" name="invoice_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Initial Remarks</label>
                                            <textarea name="remarks" class="form-control form-control-sm" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary btn-block">Generate Invoice Record</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Targets Tab -->
                        <div class="tab-pane" id="tab_targets">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 100px;">FY</th>
                                                    <th style="width: 80px;">Type</th>
                                                    <th>Prev FY</th>
                                                    <th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th><th>Sep</th>
                                                    <th>Oct</th><th>Nov</th><th>Dec</th><th>Jan</th><th>Feb</th><th>Mar</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="targets-table-body">
                                                @forelse($project->projectTargets->sortByDesc('financial_year') as $target)
                                                    @php
                                                        $actuals = $targetActuals[$target->financial_year] ?? null;
                                                    @endphp
                                                    <!-- Target Row -->
                                                    <tr class="bg-white">
                                                        <td rowspan="2" class="align-middle font-weight-bold">{{ $target->financial_year }}</td>
                                                        <td class="text-primary font-weight-bold">Target</td>
                                                        <td class="text-muted">{{ number_format($target->billed_prev_fy, 0) }}</td>
                                                        @foreach(['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $m)
                                                            <td>{{ number_format($target->{$m}, 0) }}</td>
                                                        @endforeach
                                                        <td class="bg-light font-weight-bold">{{ number_format($target->total_target, 0) }}</td>
                                                    </tr>
                                                    <!-- Actual Row -->
                                                    <tr class="bg-light">
                                                        <td class="text-success font-weight-bold">Actual</td>
                                                        <td>-</td>
                                                        @foreach(['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $m)
                                                            @php
                                                                $val = $actuals[$m] ?? 0;
                                                                $tVal = $target->{$m};
                                                                $colorClass = $val >= $tVal && $tVal > 0 ? 'text-success' : ($val < $tVal && $tVal > 0 ? 'text-danger' : '');
                                                            @endphp
                                                            <td class="{{ $colorClass }} font-weight-bold">{{ number_format($val, 0) }}</td>
                                                        @endforeach
                                                        <td class="font-weight-bold text-success">{{ number_format($actuals['total_actual'] ?? 0, 0) }}</td>
                                                    </tr>
                                                    @if($target->remarks)
                                                        <tr>
                                                            <td colspan="16" class="p-1 text-left"><small class="text-muted ml-2"><b>Remarks:</b> {{ $target->remarks }}</small></td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr><td colspan="16" class="text-center text-muted">No targets defined yet.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="card card-default mt-3 shadow-none border">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-edit"></i> Set/Update Monthly Targets</h3>
                                            <div class="card-tools">
                                                <span class="badge badge-info">Enter full numbers (e.g. 130000 for 1.3L)</span>
                                            </div>
                                        </div>
                                        <form action="{{ route('projects.storeTarget', $project->id) }}" method="POST" class="ajax-form" id="target-form">
                                            @csrf
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Financial Year</label>
                                                            <select name="financial_year" class="form-control" required>
                                                                <option value="2025-26">2025-26</option>
                                                                <option value="2026-27" selected>2026-27</option>
                                                                <option value="2027-28">2027-28</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Billed in Prev FY</label>
                                                            <input type="number" step="0.01" name="billed_prev_fy" class="form-control" value="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    @foreach(['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $m)
                                                        <div class="col-md-2 col-sm-4">
                                                            <div class="form-group">
                                                                <label class="text-uppercase" style="font-size: 11px;">{{ $m }}</label>
                                                                <input type="number" step="0.01" name="{{ $m }}" class="form-control form-control-sm" value="0">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Remarks</label>
                                                    <textarea name="remarks" class="form-control" rows="2" placeholder="Describe how targets were defined..."></textarea>
                                                </div>
                                            </div>
                                            <div class="card-footer text-right">
                                                <button type="submit" class="btn btn-primary">Save Targets</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Team Tab -->
                        <div class="tab-pane" id="tab_team">
                            <h4>Project Team Hierarchy</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush" id="team-members-list">
                                        @foreach($hierarchy['team_members'] as $member)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $member['name'] }}
                                                <span class="badge badge-primary badge-pill">{{ $member['role'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6 border-left">
                                    <h5>Add Team Member</h5>
                                    <form action="{{ route('projects.assignTeam', $project->id) }}" method="POST" class="ajax-form" id="team-form">
                                        @csrf
                                        <div class="form-group">
                                            <label>Select User</label>
                                            <select name="user_id" class="form-control select2" required>
                                                <option value="">-- Select User --</option>
                                                @foreach(\App\Models\User::all() as $u)
                                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->getRoleNames()->first() }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success">Assign Member</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Activity Logs Tab -->
                        @can('administration.access')
                        <div class="tab-pane" id="tab_logs">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Module</th>
                                        </tr>
                                    </thead>
                                    <tbody id="activity-logs-body">
                                        @forelse($logs as $log)
                                            <tr>
                                                <td><small class="text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</small></td>
                                                <td>
                                                    <div class="user-block">
                                                        <span class="username" style="margin-left:0; font-size:14px;">{{ $log->user->name }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $log->description }}</td>
                                                <td><span class="badge badge-secondary">{{ strtoupper($log->type) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center py-4 text-muted">No activity logs recorded yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Update Modal -->
    <div class="modal fade" id="modal-record-payment">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="payment-modal-title">Record Payment</span> for Inv <span id="payment-invoice-no"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" class="ajax-form" id="payment-update-form">
                    @csrf
                    <input type="hidden" name="type" id="payment-type" value="customer">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box bg-light shadow-none border">
                                    <div class="info-box-content text-center">
                                        <span class="info-box-text text-muted">Total Amount</span>
                                        <span class="info-box-number text-primary" id="payment-total-amount">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-light shadow-none border">
                                    <div class="info-box-content text-center">
                                        <span class="info-box-text text-muted">Already Processed</span>
                                        <span class="info-box-number text-success" id="payment-received-amount">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount Processed Now</label>
                                    <input type="number" step="0.01" name="payment_amount" class="form-control" placeholder="Amount..." required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4" id="payment-note-wrapper">
                                <div class="form-group">
                                    <label>Payment Note / Ref</label>
                                    <input type="text" name="payment_note" class="form-control" placeholder="Cheque # / UTR...">
                                </div>
                            </div>
                        </div>

                        <!-- Customer Specific Deductions -->
                        <div id="customer-deductions-section">
                            <h6 class="border-bottom pb-2 mt-3 text-primary font-weight-bold">Deductions (Customer Side)</h6>
                            <div class="row">
                                <div class="col-md-2"><div class="form-group"><label>TDS (IT)</label><input type="number" step="0.01" name="customer_tds_it" class="form-control form-control-sm deduction-input" value="0"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>TDS (GST)</label><input type="number" step="0.01" name="customer_tds_gst" class="form-control form-control-sm deduction-input" value="0"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>LD</label><input type="number" step="0.01" name="customer_ld" class="form-control form-control-sm deduction-input" value="0"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Any Other</label><input type="number" step="0.01" name="customer_any_other" class="form-control form-control-sm deduction-input" value="0"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Total Deduction</label><input type="number" step="0.01" class="form-control form-control-sm bg-light" id="customer-deduction-total" value="0" readonly></div></div>
                            </div>
                        </div>

                        <!-- Vendor Specific Deductions -->
                        <div id="vendor-deductions-section" style="display: none;">
                            <h6 class="border-bottom pb-2 mt-3 text-orange font-weight-bold">Deductions (Vendor Side)</h6>
                            <div class="row">
                                <div class="col-md-2"><div class="form-group"><label>TDS</label><input type="number" step="0.01" name="tds_deduction" class="form-control form-control-sm vendor-deduction-input" value="0"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>GST-TDS</label><input type="number" step="0.01" name="gst_tds_deduction" class="form-control form-control-sm vendor-deduction-input" value="0"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>Bank</label><input type="number" step="0.01" name="bank_charges" class="form-control form-control-sm vendor-deduction-input" value="0"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>TA / DA</label><input type="number" step="0.01" name="ta_da" class="form-control form-control-sm vendor-deduction-input" value="0"></div></div>
                                <div class="col-md-4"><div class="form-group"><label>Total Deduction</label><input type="number" step="0.01" class="form-control form-control-sm bg-light" id="vendor-deduction-total" value="0" readonly></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="payment-submit-btn">Record Receipt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@8/dist/sweetalert2.min.css">
<style>
    .loading-overlay {
        position: relative;
        opacity: 0.6;
        pointer-events: none;
    }
    .loading-overlay::after {
        content: "Processing...";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255,255,255,0.8);
        padding: 5px 15px;
        border-radius: 4px;
        font-weight: bold;
        z-index: 1000;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script>
$(function() {
    var currentUserName = "{{ auth()->user()->name }}";

    // Handle "Record Payment" button click (Delegated)
    $(document).on('click', '.btn-record-payment', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const no = $(this).data('no');
        const received = $(this).data('received');
        const total = $(this).data('total');
        const projectId = "{{ $project->id }}";
        
        const $modal = $('#modal-record-payment');
        const $form = $('#payment-update-form');
        
        $('#payment-type').val(type);
        $('#payment-invoice-no').text(no);
        $('#payment-total-amount').text(parseFloat(total).toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#payment-received-amount').text(parseFloat(received).toLocaleString(undefined, {minimumFractionDigits: 2}));
        
        if (type === 'vendor') {
            $('#payment-modal-title').text('Record Vendor Payout');
            $('#payment-submit-btn').text('Record Payout').removeClass('btn-primary').addClass('btn-info');
            $('#customer-deductions-section').hide();
            $('#vendor-deductions-section').show();
            $('#payment-note-wrapper').show();
        } else {
            $('#payment-modal-title').text('Record Customer Receipt');
            $('#payment-submit-btn').text('Record Receipt').removeClass('btn-info').addClass('btn-primary');
            $('#customer-deductions-section').show();
            $('#vendor-deductions-section').hide();
            $('#payment-note-wrapper').hide();
        }

        $form.attr('action', `/projects/${projectId}/invoices/${id}/payment`);
        $modal.modal('show');
    });

    // Auto-calculate Customer Deductions
    $(document).on('input', '.deduction-input', function() {
        let total = 0;
        $('#customer-deductions-section .deduction-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#customer-deduction-total').val(total.toFixed(2));
    });

    // Auto-calculate Vendor Deductions
    $(document).on('input', '.vendor-deduction-input', function() {
        let total = 0;
        $('#vendor-deductions-section .vendor-deduction-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#vendor-deduction-total').val(total.toFixed(2));
    });

    // Shared AJAX submit handler
    $('#document-type-select').change(function() {
        if ($(this).val() === 'other') {
            $('#custom-type-group').show().find('input').attr('required', true);
        } else {
            $('#custom-type-group').hide().find('input').attr('required', false);
        }
    });

    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const formId = $form.attr('id');
        const formData = new FormData(this);
        const submitBtn = $form.find('button[type="submit"]');

        // Show loading
        $form.addClass('loading-overlay');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        type: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Update UI based on form ID
                    handleResponseData(formId, response.data, $form);
                    
                    // Update dashboard widgets
                    updateDashboardWidgets(formId, response.data, $form);
                    
                    // Add a log entry row automatically
                    appendActivityLog(formId, response.data, $form);

                    // Clear form
                    $form[0].reset();
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred. Please try again.';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    html: errorMsg
                });
            },
            complete: function() {
                $form.removeClass('loading-overlay');
                submitBtn.prop('disabled', false);
            }
        });
    });

    function handleResponseData(formId, data, $form) {
        let tableBody = '';
        let rowHtml = '';

        switch(formId) {
            case 'capex-form':
                tableBody = $('#capex-table-body');
                removeEmptyRow(tableBody);
                rowHtml = `<tr>
                    <td>${data.completion_date || data.entry_date}</td>
                    <td>${data.remarks}</td>
                    <td>${parseFloat(data.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                </tr>`;
                tableBody.append(rowHtml);
                break;

            case 'opex-form':
                tableBody = $('#opex-table-body');
                removeEmptyRow(tableBody);
                rowHtml = `<tr>
                    <td>${data.entry_date}</td>
                    <td>${data.remarks}</td>
                    <td>${data.duration}</td>
                    <td>${parseFloat(data.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                </tr>`;
                tableBody.append(rowHtml);
                break;

            case 'documents-form':
                const category = $form.find('select[name="category"]').val();
                const targetBody = category === 'customer' ? $('#documents-customer-table-body') : $('#documents-vendor-table-body');
                removeEmptyRow(targetBody);
                // Data is an array for documents
                data.forEach(doc => {
                    rowHtml = `<tr>
                        <td>${doc.file_name}</td>
                        <td><span class="badge badge-secondary">${doc.type.toUpperCase()}</span></td>
                        <td><a href="/documents/${doc.id}/download" class="btn btn-xs btn-outline-primary"><i class="fas fa-download"></i> Download</a></td>
                    </tr>`;
                    targetBody.append(rowHtml);
                });
                $('#custom-type-group').hide();
                break;

            case 'bg-form':
                tableBody = $('#bg-table-body');
                removeEmptyRow(tableBody);
                rowHtml = `<tr>
                    <td>${data.bg_no || 'N/A'}</td>
                    <td>${data.bg_date || 'N/A'}</td>
                    <td>${data.type}</td>
                    <td>${parseFloat(data.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td>${data.validity_date}</td>
                    <td><span class="badge badge-success text-capitalize">${data.status}</span></td>
                </tr>`;
                tableBody.append(rowHtml);
                break;

            case 'invoices-form':
                const custBody = $('#invoices-customer-table-body');
                const vendBody = $('#invoices-vendor-table-body');
                removeEmptyRow(custBody);
                removeEmptyRow(vendBody);

                const formatC = (n) => parseFloat(n || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
                
                // Add to Customer Table
                const custRow = `<tr data-invoice-id="${data.id}">
                    <td>${data.cel_invoice_no || '-'}</td>
                    <td>${data.invoice_date}</td>
                    <td class="text-right">${formatC(data.cel_total_with_gst)}</td>
                    <td class="text-right text-success font-weight-bold">${formatC(data.payment_received)}</td>
                    <td class="text-right text-muted" style="font-size: 11px;">${formatC(data.customer_tds_it)} / ${formatC(data.customer_tds_gst)}</td>
                    <td class="text-right text-muted" style="font-size: 11px;">${formatC(data.customer_ld)} / ${formatC(data.customer_any_other)}</td>
                    <td class="text-center"><span class="badge badge-status ${data.status == 'paid' ? 'badge-success' : 'badge-warning'} text-capitalize">${data.status}</span></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-outline-success btn-record-payment" 
                                data-id="${data.id}" data-type="customer" data-no="${data.cel_invoice_no}"
                                data-received="${data.payment_received}" data-total="${data.cel_total_with_gst}" title="Record Receipt">
                            <i class="fas fa-plus-circle"></i> Receipt
                        </button>
                    </td>
                </tr>`;
                custBody.append(custRow);

                // Add to Vendor Table
                const vendRow = `<tr data-invoice-id="${data.id}">
                    <td>${data.vendor_invoice_no}</td>
                    <td><small>${data.work_description || '-'}</small></td>
                    <td class="text-right">${formatC(data.vendor_total_with_gst)}</td>
                    <td class="text-right text-primary font-weight-bold">${formatC(data.vendor_paid_amount || 0)}</td>
                    <td class="text-right text-muted" style="font-size: 11px;">${formatC(data.tds_deduction)} / ${formatC(data.gst_tds_deduction)}</td>
                    <td class="text-right text-muted" style="font-size: 11px;">${formatC(data.bank_charges)} / ${formatC(data.ta_da)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-xs btn-outline-primary btn-record-payment" 
                                data-id="${data.id}" data-type="vendor" data-no="${data.vendor_invoice_no}"
                                data-received="${data.vendor_paid_amount || 0}" data-total="${data.vendor_total_with_gst}" title="Record Payout">
                            <i class="fas fa-paper-plane"></i> Payout
                        </button>
                    </td>
                </tr>`;
                vendBody.append(vendRow);
                break;

            case 'payment-update-form':
                const rows = $(`tr[data-invoice-id="${data.id}"]`);
                const fN = (n) => parseFloat(n || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
                
                rows.each(function() {
                    const $r = $(this);
                    const btn = $r.find('.btn-record-payment');
                    const type = btn.data('type');
                    
                    if (type === 'customer') {
                        $r.find('td:nth-child(4)').text(fN(data.payment_received));
                        $r.find('td:nth-child(5)').text(`${fN(data.customer_tds_it)} / ${fN(data.customer_tds_gst)}`);
                        $r.find('td:nth-child(6)').text(`${fN(data.customer_ld)} / ${fN(data.customer_any_other)}`);
                        btn.data('received', data.payment_received).attr('data-received', data.payment_received);
                    } else {
                        $r.find('td:nth-child(4)').text(fN(data.vendor_paid_amount));
                        $r.find('td:nth-child(5)').text(`${fN(data.tds_deduction)} / ${fN(data.gst_tds_deduction)}`);
                        $r.find('td:nth-child(6)').text(`${fN(data.bank_charges)} / ${fN(data.ta_da)}`);
                        btn.data('received', data.vendor_paid_amount).attr('data-received', data.vendor_paid_amount);
                    }
                    
                    const badge = $r.find('.badge-status');
                    if (badge.length) {
                        badge.text(data.status.charAt(0).toUpperCase() + data.status.slice(1));
                        badge.removeClass('badge-warning badge-success').addClass(data.status === 'paid' ? 'badge-success' : 'badge-warning');
                    }
                });
                
                $('#modal-record-payment').modal('hide');
                break;

            case 'team-form':
                const teamList = $('#team-members-list');
                rowHtml = `<li class="list-group-item d-flex justify-content-between align-items-center">
                    ${data.name}
                    <span class="badge badge-primary badge-pill">${data.role}</span>
                </li>`;
                teamList.append(rowHtml);
                break;

            case 'target-form':
                tableBody = $('#targets-table-body');
                const fy = data.financial_year;
                // Check if row already exists for this FY
                let existingRow = tableBody.find(`tr:contains('${fy}')`);
                
                const format = (val) => parseFloat(val || 0).toLocaleString(undefined, {minimumFractionDigits: 0});
                
                rowHtml = `<tr>
                    <td>${fy}</td>
                    <td>${format(data.billed_prev_fy)}</td>
                    <td>${format(data.apr)}</td><td>${format(data.may)}</td><td>${format(data.jun)}</td>
                    <td>${format(data.jul)}</td><td>${format(data.aug)}</td><td>${format(data.sep)}</td>
                    <td>${format(data.oct)}</td><td>${format(data.nov)}</td><td>${format(data.dec)}</td>
                    <td>${format(data.jan)}</td><td>${format(data.feb)}</td><td>${format(data.mar)}</td>
                    <td class="font-weight-bold text-primary">${format(data.total_target)}</td>
                    <td><small>${data.remarks || ''}</small></td>
                </tr>`;

                if (existingRow.length) {
                    existingRow.replaceWith(rowHtml);
                } else {
                    removeEmptyRow(tableBody);
                    tableBody.prepend(rowHtml);
                }
                break;
        }
    }

    function updateDashboardWidgets(formId, data, $form) {
        const parse = (val) => parseFloat(val || 0);
        
        if (formId === 'capex-form') {
            const el = $('#widget-capex');
            const current = parse(el.text().replace(/,/g, ''));
            el.text((current + parse(data.amount)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        } 
        else if (formId === 'invoices-form') {
            const elBilling = $('#widget-billing');
            const elReceived = $('#widget-received');
            const elOutstanding = $('#widget-outstanding');
            
            const currentBilling = parse(elBilling.text().replace(/,/g, ''));
            const currentReceived = parse(elReceived.text().replace(/,/g, ''));
            
            const newBilling = currentBilling + parse(data.cel_total);
            const newReceived = currentReceived + parse(data.payment_received);
            const newOutstanding = newBilling - newReceived;
            
            elBilling.text(newBilling.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            elReceived.text(newReceived.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            elOutstanding.text(newOutstanding.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }
        else if (formId === 'payment-update-form') {
            const elReceived = $('#widget-received');
            const elOutstanding = $('#widget-outstanding');
            
            const type = $form.find('input[name="type"]').val();
            if (type === 'customer') {
                const amountPaidNow = parse($form.find('input[name="payment_amount"]').val());
                const currentReceived = parse(elReceived.text().replace(/,/g, ''));
                const currentOutstanding = parse(elOutstanding.text().replace(/,/g, ''));

                const newReceived = currentReceived + amountPaidNow;
                const newOutstanding = currentOutstanding - amountPaidNow;

                elReceived.text(newReceived.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                elOutstanding.text(newOutstanding.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }
        }
    }

    function appendActivityLog(formId, data, $form) {
        const logsBody = $('#activity-logs-body');
        const detailsTimeline = $('#details-timeline');
        const teamList = $('#details-team-list');
        
        removeEmptyRow(logsBody);
        
        let type = '';
        let desc = '';
        const now = new Date().toISOString().slice(0, 19).replace('T', ' ');

        const addRow = (type, desc) => {
            // Main log table
            const html = `<tr>
                <td><small class="text-muted">${now}</small></td>
                <td>
                    <div class="user-block">
                        <span class="username" style="margin-left:0; font-size:14px;">${currentUserName}</span>
                    </div>
                </td>
                <td>${desc}</td>
                <td><span class="badge badge-secondary">${type.toUpperCase()}</span></td>
            </tr>`;
            logsBody.prepend(html);

            // Mini timeline in details tab
            if (detailsTimeline.length) {
                const timelineItem = `<div>
                    <i class="fas fa-check-circle bg-secondary"></i>
                    <div class="timeline-item shadow-none border" style="margin-left: 40px; margin-right: 0;">
                        <span class="time"><i class="far fa-clock"></i> Just now</span>
                        <h3 class="timeline-header" style="font-size: 13px;"><b>${currentUserName}</b> ${desc}</h3>
                    </div>
                </div>`;
                detailsTimeline.prepend(timelineItem);
                detailsTimeline.find('p.text-muted').remove();
            }
        };

        switch(formId) {
            case 'capex-form':
                addRow('capex', `Added CAPEX entry of amount ${data.amount} with remarks: ${data.remarks}`);
                break;
            case 'opex-form':
                addRow('opex', `Added OPEX entry of amount ${data.amount} for duration ${data.duration}`);
                break;
            case 'documents-form':
                data.forEach(doc => {
                    addRow('document', `Uploaded document: ${doc.file_name} (Type: ${doc.type.toUpperCase()})`);
                });
                break;
            case 'bg-form':
                addRow('bg', `Recorded ${data.type} Bank Guarantee: ${data.bg_no} for amount ${data.amount}`);
                break;
            case 'invoices-form':
                addRow('invoice', `Recorded Invoice: ${data.vendor_invoice_no} (Vendor) / ${data.cel_invoice_no || 'N/A'} (CEL)`);
                break;
            case 'team-form':
                addRow('team', `Assigned team member: ${data.name} (${data.role})`);
                if (teamList.length) {
                    teamList.find('.text-muted').remove();
                    teamList.append(`<div class="mb-1"><i class="fas fa-user-circle text-muted"></i> ${data.name}</div>`);
                }
                break;
            case 'target-form':
                addRow('target', `Updated financial targets for FY ${data.financial_year}. Total target: ${data.total_target}`);
                break;
            case 'payment-update-form':
                const paidNow = $form.find('input[name="payment_amount"]').val();
                addRow('invoice', `Recorded payment update: ${parseFloat(paidNow).toLocaleString(undefined, {minimumFractionDigits: 2})} for Invoice: ${data.vendor_invoice_no}. Total Received: ${parseFloat(data.payment_received).toLocaleString(undefined, {minimumFractionDigits: 2})}, Status: ${data.status.toUpperCase()}`);
                break;
        }
    }

    function removeEmptyRow(tbody) {
        if (tbody.find('tr').length === 1 && tbody.find('td').length === 1) {
            tbody.empty();
        }
    }
});
</script>
@stop
