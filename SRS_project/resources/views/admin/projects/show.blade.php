@extends('adminlte::page')

@section('title', 'Project Details')

@section('content_header')
    <h1>Project: {{ $project->name }} <small>({{ strtoupper($project->financial_type) }} / {{ strtoupper($project->project_type) }})</small></h1>
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
                        <li class="nav-item"><a class="nav-link" href="#tab_team" data-toggle="tab">Team</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Details Tab -->
                        <div class="tab-pane active" id="tab_details">
                            <dl class="row">
                                <dt class="col-sm-3">Department</dt>
                                <dd class="col-sm-9">{{ $project->department->name ?? 'None' }}</dd>

                                <dt class="col-sm-3">Financial Type</dt>
                                <dd class="col-sm-3">{{ ucfirst($project->financial_type) }}</dd>

                                <dt class="col-sm-3">Project Type</dt>
                                <dd class="col-sm-3">{{ ucfirst($project->project_type) }}</dd>
                                
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
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>CAPEX Entries</h4>
                                    <table class="table table-bordered mb-4">
                                        <thead><tr><th>Date</th><th>Description</th><th>Amount</th></tr></thead>
                                        <tbody>
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
                                        <thead><tr><th>Date</th><th>Description</th><th>Amount</th></tr></thead>
                                        <tbody>
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
                                    <form action="{{ route('projects.storeCapex', $project->id) }}" method="POST" class="mb-4">
                                        @csrf
                                        <div class="form-group"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                                        <div class="form-group"><input type="text" name="remarks" class="form-control" placeholder="Remarks" required></div>
                                        <div class="form-group"><label>Completion Date</label><input type="date" name="completion_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                                        <button type="submit" class="btn btn-sm btn-primary">Add CAPEX</button>
                                    </form>

                                    <hr>

                                    <h5>Add OPEX Entry</h5>
                                    <form action="{{ route('projects.storeOpex', $project->id) }}" method="POST">
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
                                        <tbody>
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
                                    <form action="{{ route('documents.store', $project->id) }}" method="POST" enctype="multipart/form-data">
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
                                        <tbody>
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
                                    <form action="{{ route('projects.storeBG', $project->id) }}" method="POST">
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
                                        <tbody>
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
                                    <form action="{{ route('projects.storeInvoice', $project->id) }}" method="POST">
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
                                    <ul class="list-group list-group-flush">
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
                                    <form action="{{ route('projects.assignTeam', $project->id) }}" method="POST">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
