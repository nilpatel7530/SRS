@extends('adminlte::page')

@section('title', 'Create Project')

@section('content_header')
    <h1>Create Project</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Project Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label>Department</label>
                    <select class="form-control @error('department_id') is-invalid @enderror" name="department_id" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Financial Type</label>
                            <select class="form-control @error('financial_type') is-invalid @enderror" name="financial_type" required>
                                <option value="">Select Financial Type</option>
                                <option value="capex" {{ old('financial_type') == 'capex' ? 'selected' : '' }}>Capex</option>
                                <option value="opex" {{ old('financial_type') == 'opex' ? 'selected' : '' }}>Opex</option>
                            </select>
                            @error('financial_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Project Type</label>
                            <select class="form-control @error('project_type') is-invalid @enderror" name="project_type" required>
                                <option value="">Select Project Type</option>
                                <option value="service" {{ old('project_type') == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="supply" {{ old('project_type') == 'supply' ? 'selected' : '' }}>Supply</option>
                            </select>
                            @error('project_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Project</button>
                <a href="{{ route('projects.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@stop
