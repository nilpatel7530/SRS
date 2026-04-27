@extends('adminlte::page')

@section('title', 'Projects')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Projects</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Project</a>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('projects.index') }}" method="GET" class="row">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Search Project</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>ISD</label>
                        <select name="department_id" class="form-control form-control-sm">
                            <option value="">All ISDs</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Financial Type</label>
                        <select name="financial_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <option value="capex" {{ request('financial_type') == 'capex' ? 'selected' : '' }}>Capex</option>
                            <option value="opex" {{ request('financial_type') == 'opex' ? 'selected' : '' }}>Opex</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Project Type</label>
                        <select name="project_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <option value="service" {{ request('project_type') == 'service' ? 'selected' : '' }}>Service</option>
                            <option value="supply" {{ request('project_type') == 'supply' ? 'selected' : '' }}>Supply</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-default">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>ISD</th>
                        <th>Financial Type</th>
                        <th>Project Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->department->name ?? 'N/A' }}</td>
                            <td><span class="badge badge-info">{{ strtoupper($project->financial_type) }}</span></td>
                            <td>{{ ucfirst($project->project_type) }}</td>
                            <td>
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-info">View</a>
                                @can('projects.edit')
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endcan
                                @can('projects.delete')
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No projects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($projects->hasPages())
            <div class="card-footer clearfix">
                {{ $projects->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@stop
