@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Roles</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
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
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                @foreach($role->permissions as $permission)
                                    <span class="badge badge-info">{{ $permission->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @if($role->name !== 'Admin')
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
