@extends('adminlte::page')

@section('title', 'Edit ISD')

@section('content_header')
    <h1>Edit ISD: {{ $department->name }}</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('departments.update', $department->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">ISD Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning">Update ISD</button>
                <a href="{{ route('departments.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@stop
