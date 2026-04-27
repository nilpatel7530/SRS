@extends('adminlte::page')

@section('title', 'Create Proposal')

@section('content_header')
    <h1>Create Project Proposal</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <form action="{{ route('proposals.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Tender Name</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_type">Project Type</label>
                                    <input type="text" name="project_type" id="project_type" class="form-control" value="{{ old('project_type') }}" placeholder="e.g. Solar, Energy, Infrastructure">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estimated_value">Estimated Value (Lakhs)</label>
                                    <input type="number" step="0.01" name="estimated_value" id="estimated_value" class="form-control @error('estimated_value') is-invalid @enderror" value="{{ old('estimated_value') }}">
                                    @error('estimated_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_name">Vendor Name</label>
                                    <input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{ old('vendor_name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="work_order_date">Work Order Date</label>
                                    <input type="date" name="work_order_date" id="work_order_date" class="form-control" value="{{ old('work_order_date') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sent_by">Sent By</label>
                                    <input type="text" name="sent_by" id="sent_by" class="form-control" value="{{ old('sent_by') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Open">Open</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Awarded">Awarded</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description_of_work">Description of Work</label>
                            <textarea name="description_of_work" id="description_of_work" class="form-control" rows="3">{{ old('description_of_work') }}</textarea>
                        </div>

                        <hr>
                        <h5><i class="fas fa-file-upload mr-2"></i>Proposal Uploads (Max 4 Revisions)</h5>
                        <div class="row">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="col-md-6 mb-2">
                                    <label>Proposal Revision {{ $i }}</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="proposals[]" class="custom-file-input">
                                            <label class="custom-file-label">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="form-group mt-3">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Proposal Hub</button>
                        <a href="{{ route('proposals.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
