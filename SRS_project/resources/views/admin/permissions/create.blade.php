@extends('adminlte::page')

@section('title', 'Create Permission')

@section('content_header')
    <h1>Create Permission</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Permission Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="e.g., manage-users" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('permissions.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@stop
