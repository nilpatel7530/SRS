@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Users</h1>
        @can('users.create')
            <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
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
        <div class="card-body table-responsive">
            <table class="table table-hover text-nowrap datatable" id="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loaded via AJAX --}}
                </tbody>
            </table>
        </div>
    </div>
@stop

@php
    $ajaxUrl = route('users.index');
    $hideExports = true;
    $columns = [
        ['data' => 'id', 'name' => 'id'],
        ['data' => 'name', 'name' => 'name'],
        ['data' => 'email', 'name' => 'email'],
        ['data' => 'roles_list', 'name' => 'roles_list', 'orderable' => false, 'searchable' => false],
        ['data' => 'department.name', 'name' => 'department.name', 'defaultContent' => 'N/A'],
        ['data' => 'status_label', 'name' => 'status_label', 'orderable' => false, 'searchable' => false],
        ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
    ];
@endphp

@include('partials.datatables-init', ['ajaxUrl' => $ajaxUrl, 'columns' => $columns, 'hideExports' => $hideExports])

@push('js')
<script>
$(document).ready(function() {
    var table = $('#users-table').DataTable();

    // Toggle Status AJAX
    $(document).on('click', '.toggle-status', function() {
        var id = $(this).data('id');
        var btn = $(this);
        
        $.ajax({
            url: "{{ url('users') }}/" + id + "/toggle-status",
            type: 'PATCH',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    table.ajax.reload(null, false); // Reload without resetting paging
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Something went wrong');
            }
        });
    });

    // Delete User AJAX
    $(document).on('click', '.delete-user', function() {
        if(!confirm('Are you sure you want to delete this user?')) return;
        
        var id = $(this).data('id');
        $.ajax({
            url: "{{ url('users') }}/" + id,
            type: 'DELETE',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    table.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Something went wrong');
            }
        });
    });
});
</script>
@endpush
