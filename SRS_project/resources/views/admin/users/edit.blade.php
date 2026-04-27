@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User: {{ $user->name }}</h1>
@stop

@section('content')
    <div class="card card-primary">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password (Leave blank to keep current password)</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>

                <div class="form-group">
                    <label>Roles</label>
                    <select class="form-control" name="roles[]" multiple>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ in_array($role->name, $userRoles) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Reporting Manager</label>
                    <select class="form-control" name="manager_id">
                        <option value="">-- No Manager --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ $user->manager_id == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }} ({{ $manager->roles->first()->name ?? 'No Role' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>ISD</label>
                    <select class="form-control" name="department_id">
                        <option value="">-- Select ISD --</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-warning">Update User</button>
                <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
@stop
