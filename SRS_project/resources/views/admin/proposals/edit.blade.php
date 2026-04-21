@extends('adminlte::page')

@section('title', 'Edit Proposal')

@section('content_header')
    <h1>Edit Proposal: {{ $proposal->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <form action="{{ route('proposals.update', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Customer / Project Name</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $proposal->name) }}" required>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_type">Project Type</label>
                                    <input type="text" name="project_type" id="project_type" class="form-control" value="{{ old('project_type', $proposal->project_type) }}" placeholder="e.g. Solar, Energy, Infrastructure">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" name="state" id="state" class="form-control" value="{{ old('state', $proposal->state) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estimated_value">Estimated Value (Lakhs)</label>
                                    <input type="number" step="0.01" name="estimated_value" id="estimated_value" class="form-control @error('estimated_value') is-invalid @enderror" value="{{ old('estimated_value', $proposal->estimated_value) }}">
                                    @error('estimated_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_name">Vendor Name</label>
                                    <input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{ old('vendor_name', $proposal->vendor_name) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="work_order_date">Work Order Date</label>
                                    <input type="date" name="work_order_date" id="work_order_date" class="form-control" value="{{ old('work_order_date', $proposal->work_order_date) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sent_by">Sent By</label>
                                    <input type="text" name="sent_by" id="sent_by" class="form-control" value="{{ old('sent_by', $proposal->sent_by) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Open" {{ old('status', $proposal->status) == 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="Closed" {{ old('status', $proposal->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                        <option value="Awarded" {{ old('status', $proposal->status) == 'Awarded' ? 'selected' : '' }}>Awarded</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description_of_work">Description of Work</label>
                            <textarea name="description_of_work" id="description_of_work" class="form-control" rows="3">{{ old('description_of_work', $proposal->description_of_work) }}</textarea>
                        </div>

                        <hr>
                        <h5><i class="fas fa-file-alt mr-2"></i>Existing Proposals</h5>
                        @if($proposal->documents->count() > 0)
                            <div class="list-group mb-3">
                                @foreach($proposal->documents as $doc)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-file-pdf text-danger mr-2"></i>
                                            {{ $doc->name }}
                                        </span>
                                        <a href="{{ Storage::url($doc->path) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No documents uploaded yet.</p>
                        @endif

                        <hr>
                        <h5><i class="fas fa-file-upload mr-2"></i>Upload New Revisions</h5>
                        <div class="row">
                            @php $remaining = 4 - $proposal->documents->count(); @endphp
                            @if($remaining > 0)
                                @for($i = 1; $i <= $remaining; $i++)
                                    <div class="col-md-6 mb-2">
                                        <label>Add Revision</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="proposals[]" class="custom-file-input">
                                                <label class="custom-file-label">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @else
                                <div class="col-md-12">
                                    <div class="alert alert-warning py-2">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Maximum 4 revisions reached.
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group mt-3">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="2">{{ old('remarks', $proposal->remarks) }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Proposal</button>
                        <a href="{{ route('proposals.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    // Show filename in custom-file-input
    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('custom-file-input')) {
            var fileName = e.target.files[0].name;
            var label = e.target.nextElementSibling;
            label.innerHTML = fileName;
        }
    });
</script>
@endpush
