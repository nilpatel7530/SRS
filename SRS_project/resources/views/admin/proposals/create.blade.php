@extends('adminlte::page')

@section('title', 'Create Proposal')

@section('content_header')
    <h1>Create Project Proposal</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <form action="{{ route('proposals.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Proposal Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="estimated_value">Estimated Value ($)</label>
                            <input type="number" step="0.01" name="estimated_value" id="estimated_value" class="form-control @error('estimated_value') is-invalid @enderror" value="{{ old('estimated_value') }}">
                            @error('estimated_value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="submission_date">Submission Date</label>
                            <input type="date" name="submission_date" id="submission_date" class="form-control @error('submission_date') is-invalid @enderror" value="{{ old('submission_date') }}">
                            @error('submission_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Proposal</button>
                        <a href="{{ route('proposals.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
