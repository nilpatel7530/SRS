@extends('adminlte::page')

@section('title', 'Permissions')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Permissions <small class="text-muted fs-6">Matrix</small></h1>
        @can('permissions.create')
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Create Permission
            </a>
        @endcan
    </div>
@stop

@section('content')
    @include('partials.alerts')
    
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-shield-alt text-primary mr-2"></i>System Permissions Matrix
            </h3>
            <div class="card-tools">
                <div class="row">
                    <div class="col-md-6 pr-1">
                        <select id="module-filter" class="form-control form-control-sm">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 pl-1">
                        <select id="action-filter" class="form-control form-control-sm">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0" id="permissions-table">
                <thead class="bg-light">
                    <tr>
                        <th><i class="fas fa-layer-group mr-1 text-primary"></i>Module / Page</th>
                        <th><i class="fas fa-cog mr-1 text-secondary"></i>Action</th>
                        <th>Permission Key</th>
                        <th>Guard</th>
                        <th width="100" class="text-center">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loaded via AJAX --}}
                </tbody>
            </table>
        </div>
    </div>
@stop

@include('partials.datatables-init', [
    'id'          => 'permissions-table',
    'ajaxUrl'     => route('permissions.index'),
    'hideExports' => true,
    'ajaxData'    => 'function(d) {
        d.module = $("#module-filter").val();
        d.action_filter = $("#action-filter").val();
    }',
    'columns' => [
        ['data' => 'module',       'name' => 'module'],
        ['data' => 'action_name',  'name' => 'action_name'],
        ['data' => 'name',         'name' => 'name'],
        ['data' => 'guard_name',   'name' => 'guard_name'],
        ['data' => 'action',       'name' => 'action', 'orderable' => false, 'searchable' => false],
    ]
])

@push('js')
<script>
    $('#module-filter, #action-filter').change(function() {
        $('#permissions-table').DataTable().ajax.reload();
    });

    $(document).on('click', '.delete-permission', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this permission?')) {
            $.ajax({
                url: "{{ url('permissions') }}/" + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#permissions-table').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                }
            });
        }
    });
</script>
@endpush
