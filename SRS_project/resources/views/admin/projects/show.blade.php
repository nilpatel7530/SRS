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
                        <li class="nav-item"><a class="nav-link" href="#tab_documents" data-toggle="tab">Documents</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_bgs" data-toggle="tab">Bank Guarantees</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab_invoices" data-toggle="tab">Invoices</a></li>
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
                                        <div class="col-sm-5 text-muted font-weight-bold"><i class="fas fa-building fa-fw mr-1"></i> Department</div>
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
                                    <table class="table table-striped">
                                        <thead><tr><th>Name</th><th>Type</th><th>Actions</th></tr></thead>
                                        <tbody id="documents-table-body">
                                            @forelse($project->documents as $doc)
                                                <tr>
                                                    <td>{{ $doc->file_name }}</td>
                                                    <td><span class="badge badge-secondary">{{ strtoupper($doc->type) }}</span></td>
                                                    <td><a href="{{ route('documents.download', $doc->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> Download</a></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3">No documents attached.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 border-left">
                                    <h5>Upload Documents</h5>
                                    <form action="{{ route('documents.store', $project->id) }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="documents-form">
                                        @csrf
                                        <div class="form-group">
                                            <label>Document Type</label>
                                            <select name="type" class="form-control" required>
                                                <option value="po">Purchase Order (PO)</option>
                                                <option value="bg">Bank Guarantee (BG)</option>
                                                <option value="invoice">Invoice</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Files</label>
                                            <input type="file" name="files[]" class="form-control-file" multiple required>
                                            <small class="text-muted">Max 50MB per file</small>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary">Upload Files</button>
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
                                    <table class="table table-striped">
                                        <thead><tr><th>Invoice Nos (V/C)</th><th>Vendor Total</th><th>CEL Total</th><th>Received</th><th>Status</th><th>Date</th></tr></thead>
                                        <tbody id="invoices-table-body">
                                            @forelse($project->invoices as $invoice)
                                                <tr>
                                                    <td>{{ $invoice->vendor_invoice_no }} / {{ $invoice->cel_invoice_no ?? '-' }}</td>
                                                    <td>{{ number_format($invoice->vendor_total_with_gst, 2) }}</td>
                                                    <td>{{ number_format($invoice->cel_total_with_gst, 2) }}</td>
                                                    <td>{{ number_format($invoice->payment_received, 2) }}</td>
                                                    <td><span class="badge {{ $invoice->status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($invoice->status) }}</span></td>
                                                    <td>{{ $invoice->invoice_date }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6">No Invoices.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3 border-left">
                                    <h5>Record Invoice</h5>
                                    <form action="{{ route('projects.storeInvoice', $project->id) }}" method="POST" class="ajax-form" id="invoices-form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-6"><input type="text" name="vendor_invoice_no" class="form-control mb-2" placeholder="Vendor Inv #" required></div>
                                            <div class="col-6"><input type="text" name="cel_invoice_no" class="form-control mb-2" placeholder="CEL Inv #"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><input type="number" step="0.01" name="vendor_total" class="form-control mb-2" placeholder="Vendor Base" required></div>
                                            <div class="col-6"><input type="number" step="0.01" name="cel_total" class="form-control mb-2" placeholder="CEL Base" required></div>
                                        </div>
                                        <div class="form-group"><input type="number" step="0.01" name="payment_received" class="form-control" placeholder="Payment Received" value="0"></div>
                                        <div class="form-group"><input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                                        <div class="form-group"><textarea name="remarks" class="form-control" placeholder="Remarks"></textarea></div>
                                        <button type="submit" class="btn btn-sm btn-primary btn-block">Save Invoice</button>
                                    </form>
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
                        @endcan
                    </div>
                </div>
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

    // Shared AJAX submit handler
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

                    // Clear form
                    $form[0].reset();
                    if ($form.find('.select2').length) {
                        $form.find('.select2').val('').trigger('change');
                    }

                    // Update UI based on form ID
                    handleResponseData(formId, response.data);
                    
                    // Update dashboard widgets
                    updateDashboardWidgets(formId, response.data);
                    
                    // Add a log entry row automatically
                    appendActivityLog(formId, response.data);
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

    function handleResponseData(formId, data) {
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
                tableBody = $('#documents-table-body');
                removeEmptyRow(tableBody);
                // Data is an array for documents
                data.forEach(doc => {
                    rowHtml = `<tr>
                        <td>${doc.file_name}</td>
                        <td><span class="badge badge-secondary">${doc.type.toUpperCase()}</span></td>
                        <td><a href="/documents/download/${doc.id}" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> Download</a></td>
                    </tr>`;
                    tableBody.append(rowHtml);
                });
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
                tableBody = $('#invoices-table-body');
                removeEmptyRow(tableBody);
                const celInv = data.cel_invoice_no || '-';
                rowHtml = `<tr>
                    <td>${data.vendor_invoice_no} / ${celInv}</td>
                    <td>${parseFloat(data.vendor_total_with_gst || data.vendor_total).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td>${parseFloat(data.cel_total_with_gst || data.cel_total).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td>${parseFloat(data.payment_received).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td><span class="badge badge-warning text-capitalize">${data.status}</span></td>
                    <td>${data.invoice_date}</td>
                </tr>`;
                tableBody.append(rowHtml);
                break;

            case 'team-form':
                const teamList = $('#team-members-list');
                rowHtml = `<li class="list-group-item d-flex justify-content-between align-items-center">
                    ${data.name}
                    <span class="badge badge-primary badge-pill">${data.role}</span>
                </li>`;
                teamList.append(rowHtml);
                break;
        }
    }

    function updateDashboardWidgets(formId, data) {
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
    }

    function appendActivityLog(formId, data) {
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
