@extends('adminlte::page')

@section('title', 'Departments')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Departments Master Data</h1>
        <a href="{{ route('departments.create') }}" class="btn btn-primary">Add Department</a>
    </div>
@stop

@section('content')
    @include('partials.alerts')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mx-0">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr>
                            <td>{{ $department->id }}</td>
                            <td>{{ $department->name }}</td>
                            <td>
                                <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No master data configured. Add a department.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
