@extends('adminlte::page')

@section('title', 'Edit Project')

@section('content_header')
    <h1>Edit Project: {{ $project->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <form action="{{ route('projects.update', $project->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Project Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $project->name) }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="department_id">Department</label>
                            <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $project->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="financial_type">Financial Type</label>
                            <select name="financial_type" id="financial_type" class="form-control @error('financial_type') is-invalid @enderror" required>
                                <option value="capex" {{ old('financial_type', $project->financial_type) == 'capex' ? 'selected' : '' }}>CAPEX</option>
                                <option value="opex" {{ old('financial_type', $project->financial_type) == 'opex' ? 'selected' : '' }}>OPEX</option>
                            </select>
                            @error('financial_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="project_type">Project Type</label>
                            <select name="project_type" id="project_type" class="form-control @error('project_type') is-invalid @enderror" required>
                                <option value="service" {{ old('project_type', $project->project_type) == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="supply" {{ old('project_type', $project->project_type) == 'supply' ? 'selected' : '' }}>Supply</option>
                            </select>
                            @error('project_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Project</button>
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
