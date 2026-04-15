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
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Department</th>
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
    </div>
@stop
