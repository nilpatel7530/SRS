@extends('adminlte::page')

@section('title', 'Create Proposal')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-dark font-weight-bold">Create Project <span class="text-primary">Proposal</span></h1>
        <a href="{{ route('proposals.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <form action="{{ route('proposals.store') }}" method="POST" enctype="multipart/form-data" class="pb-5">
        @csrf
        <div class="row">
            <!-- Main Content: Full Screen Feel -->
            <div class="col-md-12">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header bg-white">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-info-circle text-primary mr-2"></i>Tender Details & Documents
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tender Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-file-signature text-primary"></i></span>
                                        </div>
                                        <input type="text" name="name" id="name" class="form-control border-left-0 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter tender/project name" required>
                                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="project_type">Project Type</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-tag text-muted"></i></span>
                                        </div>
                                        <input type="text" name="project_type" id="project_type" class="form-control border-left-0" value="{{ old('project_type') }}" placeholder="Solar, Energy...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                        </div>
                                        <input type="text" name="state" id="state" class="form-control border-left-0" value="{{ old('state') }}" placeholder="Enter state">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estimated_value">Estimated Value (Lakhs)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-rupee-sign text-success"></i></span>
                                        </div>
                                        <input type="number" step="0.01" name="estimated_value" id="estimated_value" class="form-control border-left-0 @error('estimated_value') is-invalid @enderror" value="{{ old('estimated_value') }}" placeholder="0.00">
                                        @error('estimated_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vendor_name">Vendor Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-building text-muted"></i></span>
                                        </div>
                                        <input type="text" name="vendor_name" id="vendor_name" class="form-control border-left-0" value="{{ old('vendor_name') }}" placeholder="Enter vendor name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-tasks text-primary"></i></span>
                                        </div>
                                        <select name="status" id="status" class="form-control border-left-0">
                                            <option value="Open" {{ old('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                            <option value="Closed" {{ old('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                            <option value="Awarded" {{ old('status') == 'Awarded' ? 'selected' : '' }}>Awarded</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="work_order_date">Work Order Date</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                                        </div>
                                        <input type="date" name="work_order_date" id="work_order_date" class="form-control border-left-0" value="{{ old('work_order_date') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sent_by">Sent By</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fas fa-user-tag text-muted"></i></span>
                                        </div>
                                        <input type="text" name="sent_by" id="sent_by" class="form-control border-left-0" value="{{ old('sent_by') }}" placeholder="Sender name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remarks">Internal Remarks</label>
                                    <input type="text" name="remarks" id="remarks" class="form-control" value="{{ old('remarks') }}" placeholder="Short note">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description_of_work">Description of Work</label>
                            <textarea name="description_of_work" id="description_of_work" class="form-control" rows="3" placeholder="Detailed project scope...">{{ old('description_of_work') }}</textarea>
                        </div>

                        <hr>

                        <h5 class="font-weight-bold mb-3"><i class="fas fa-paperclip text-success mr-2"></i>Documents & Revisions</h5>
                        <div class="alert alert-light border small text-muted mb-4 py-2">
                            <i class="fas fa-info-circle mr-1"></i> Max 4 revisions. Supported: PDF, DOCX, XLSX, ZIP, Images. Max 15MB each.
                        </div>

                        <div class="row">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="small font-weight-bold">Revision {{ $i + 1 }}</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="proposals[]" class="custom-file-input @error('proposals.'.$i) is-invalid @enderror" id="proposal_{{ $i }}">
                                                <label class="custom-file-label" for="proposal_{{ $i }}">Choose file</label>
                                            </div>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info preview-btn-local d-none" data-target="proposal_{{ $i }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('proposals.'.$i)
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right">
                        <a href="{{ route('proposals.index') }}" class="btn btn-link text-muted mr-3">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                            <i class="fas fa-save mr-2"></i>Create Proposal Hub
                        </button>
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
                        <img id="previewImage" src="" class="img-fluid rounded">
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

