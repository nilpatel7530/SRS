@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Roles</h1>
        @can('roles.create')
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
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
            <table class="table table-bordered table-striped" id="roles-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Permissions Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@push('js')
    @include('partials.datatables-init', [
        'id' => 'roles-table',
        'ajaxUrl' => route('roles.index'),
        'hideExports' => true,
        'columns' => [
            ['data' => 'id', 'name' => 'id'],
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'permissions_count', 'name' => 'permissions_count', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]
    ])

    <script>
        $(document).on('click', '.delete-role', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this role?')) {
                $.ajax({
                    url: "{{ url('roles') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#roles-table').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || 'Something went wrong');
                    }
                });
            }
        });
    </script>
@endpush
