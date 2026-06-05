@extends('adminlte::page')

@section('title', 'Edit Proposal')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-dark font-weight-bold">Edit Proposal: <span class="text-primary">{{ $proposal->name }}</span></h1>
        <a href="{{ route('proposals.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <form action="{{ route('proposals.update', $proposal->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Left Column: Primary Information -->
            <div class="col-lg-8">
                <div class="card card-outline card-warning shadow-sm">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-edit text-warning mr-2"></i>Edit Tender Details
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Tender Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-file-signature text-warning"></i></span>
                                </div>
                                <input type="text" name="name" id="name" class="form-control border-left-0 @error('name') is-invalid @enderror" value="{{ old('name', $proposal->name) }}" required>
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_type">Project Type</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-tag text-muted"></i></span>
                                        </div>
                                        <input type="text" name="project_type" id="project_type" class="form-control border-left-0" value="{{ old('project_type', $proposal->project_type) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                        </div>
                                        <input type="text" name="state" id="state" class="form-control border-left-0" value="{{ old('state', $proposal->state) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estimated_value">Estimated Value (Lakhs)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-rupee-sign text-success"></i></span>
                                        </div>
                                        <input type="number" step="0.01" name="estimated_value" id="estimated_value" class="form-control border-left-0 @error('estimated_value') is-invalid @enderror" value="{{ old('estimated_value', $proposal->estimated_value) }}">
                                        @error('estimated_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_name">Vendor Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-building text-muted"></i></span>
                                        </div>
                                        <input type="text" name="vendor_name" id="vendor_name" class="form-control border-left-0" value="{{ old('vendor_name', $proposal->vendor_name) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description_of_work">Description of Work</label>
                            <textarea name="description_of_work" id="description_of_work" class="form-control" rows="4">{{ old('description_of_work', $proposal->description_of_work) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-file-alt text-success mr-2"></i>Existing Documents
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if($proposal->documents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light small text-uppercase">
                                        <tr>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Uploaded At</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($proposal->documents as $doc)
                                            <tr>
                                                <td class="align-middle">
                                                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                                                    {{ $doc->file_name }}
                                                </td>
                                                <td class="align-middle"><span class="badge badge-secondary">{{ strtoupper($doc->type) }}</span></td>
                                                <td class="align-middle small text-muted">{{ $doc->created_at->format('d M Y, h:i A') }}</td>
                                                <td class="text-right">
                                                    <a href="{{ Storage::disk('public')->url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-info preview-btn" title="Preview">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ Storage::disk('public')->url($doc->file_path) }}" download class="btn btn-sm btn-outline-secondary" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No documents uploaded yet.</p>
                            </div>
                        @endif
                    </div>
                    @php $remaining = 4 - $proposal->documents->count(); @endphp
                    @if($remaining > 0)
                        <div class="card-footer bg-light border-top">
                            <h6 class="font-weight-bold small mb-3">UPLOAD NEW REVISIONS ({{ $remaining }} Slots Available)</h6>
                            <div class="row">
                                @for($i = 1; $i <= $remaining; $i++)
                                    <div class="col-md-6 mb-2">
                                        <div class="input-group input-group-sm">
                                            <div class="custom-file">
                                                <input type="file" name="proposals[]" class="custom-file-input" id="new_proposal_{{ $i }}">
                                                <label class="custom-file-label" for="new_proposal_{{ $i }}">Choose file</label>
                                            </div>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-info preview-btn-local d-none" data-target="new_proposal_{{ $i }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Metadata & Actions -->
            <div class="col-lg-4">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-cog text-secondary mr-2"></i>Management
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Current Status</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-tasks text-warning"></i></span>
                                </div>
                                <select name="status" id="status" class="form-control border-left-0">
                                    <option value="Open" {{ old('status', $proposal->status) == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ old('status', $proposal->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="Awarded" {{ old('status', $proposal->status) == 'Awarded' ? 'selected' : '' }}>Awarded</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="work_order_date">Work Order Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                                </div>
                                <input type="date" name="work_order_date" id="work_order_date" class="form-control border-left-0" value="{{ old('work_order_date', $proposal->work_order_date) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sent_by">Sent By</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-user-tag text-muted"></i></span>
                                </div>
                                <input type="text" name="sent_by" id="sent_by" class="form-control border-left-0" value="{{ old('sent_by', $proposal->sent_by) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Internal Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ old('remarks', $proposal->remarks) }}</textarea>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-warning btn-block btn-lg shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-2"></i>Update Proposal
                        </button>
                        <a href="{{ route('proposals.index') }}" class="btn btn-default btn-block mt-2">
                            Cancel
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body p-3 small text-muted">
                        <p class="mb-1"><strong>Created:</strong> {{ $proposal->created_at->format('d M Y') }}</p>
                        <p class="mb-0"><strong>Last Update:</strong> {{ $proposal->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold">File Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="previewFrame" src="" style="width:100%; height:80vh; border:none;"></iframe>
                    <div id="previewImageContainer" class="text-center p-3" style="display:none;">
                        <img id="previewImage" src="" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    $(document).ready(function() {
        // Show filename in custom-file-input and handle preview button
        $('.custom-file-input').on('change', function (e) {
            var fileName = e.target.files[0].name;
            var label = $(this).next('.custom-file-label');
            label.html(fileName);
            
            // Show preview button for the specific input
            $(this).closest('.input-group').find('.preview-btn-local').removeClass('d-none');
        });

        // Remote Preview for existing files
        $('.preview-btn').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var ext = url.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png', 'gif', 'svg'].indexOf(ext) !== -1) {
                $('#previewFrame').hide();
                $('#previewImage').attr('src', url);
                $('#previewImageContainer').show();
            } else {
                $('#previewImageContainer').hide();
                $('#previewFrame').attr('src', url).show();
            }
            $('#previewModal').modal('show');
        });

        // Local Preview for newly selected files
        $('.preview-btn-local').on('click', function() {
            var inputId = $(this).data('target');
            var file = document.getElementById(inputId).files[0];
            
            if (file) {
                var reader = new FileReader();
                var fileType = file.type;
                
                reader.onload = function(e) {
                    var url = e.target.result;
                    
                    if (fileType.match('image.*')) {
                        $('#previewFrame').hide();
                        $('#previewImage').attr('src', url);
                        $('#previewImageContainer').show();
                    } else if (fileType === 'application/pdf') {
                        $('#previewImageContainer').hide();
                        $('#previewFrame').attr('src', url).show();
                    } else {
                        toastr.info('Preview only supported for Images and PDFs. Other files can be downloaded after saving.');
                        return;
                    }
                    $('#previewModal').modal('show');
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush

