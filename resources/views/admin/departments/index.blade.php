@extends('adminlte::page')

@section('title', 'ISDs')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>ISDs Master Data</h1>
        @can('departments.create')
            <a href="{{ route('departments.create') }}" class="btn btn-primary">Add ISD</a>
        @endcan
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
        <div class="card-body">
            <table class="table table-bordered table-striped datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $department)
                        <tr>
                            <td>{{ $department->id }}</td>
                            <td>{{ $department->name }}</td>
                            <td>
                                @can('departments.edit')
                                    <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endcan
                                
                                @can('departments.delete')
                                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@include('partials.datatables-init')
