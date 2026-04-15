@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Permissions</h1>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">Create Permission</a>
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
                        <th>Guard Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->guard_name }}</td>
                            <td>
                                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
