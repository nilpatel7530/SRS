@extends('adminlte::page')

@section('title', 'Add ISD')

@section('content_header')
    <h1>Add ISD</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">ISD Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save ISD</button>
                <a href="{{ route('departments.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@stop
