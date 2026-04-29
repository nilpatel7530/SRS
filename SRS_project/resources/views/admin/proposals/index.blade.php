@extends('adminlte::page')

@section('title', 'Project Proposals')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Project Proposals</h1>
        @can('proposals.create')
            <a href="{{ route('proposals.create') }}" class="btn btn-primary btn-sm">Create Proposal</a>
        @endcan
    </div>
@endsection

@section('content')
    @include('partials.alerts')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped datatable">
                <thead>
                    <tr>
                        <th>Tender Name</th>
                        <th>Type</th>
                        <th class="text-right">Value</th>
                        <th>State</th>
                        <th>Vendor</th>
                        <th>WO Date</th>
                        <th class="text-center">Proposals</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proposals as $proposal)
                        <tr>
                            <td>
                                <strong>{{ $proposal->name }}</strong><br>
                                <small class="text-muted">By: {{ $proposal->creator->name ?? 'System' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $proposal->project_type ?? 'Solar' }}</span>
                            </td>
                            <td class="text-right">{{ number_format($proposal->estimated_value, 0) }}</td>
                            <td>{{ $proposal->state }}</td>
                            <td>{{ $proposal->vendor_name }}</td>
                            <td>{{ $proposal->work_order_date ? date('d-m-Y', strtotime($proposal->work_order_date)) : '-' }}</td>
                            <td class="text-center">
                                @foreach($proposal->documents->where('category', 'Proposal') as $index => $doc)
                                    <a href="{{ Storage::disk('public')->url($doc->file_path) }}" title="Preview Proposal {{ $index + 1 }}" class="btn btn-sm btn-outline-info p-1 preview-btn" style="font-size: 10px;">
                                        P{{ $index + 1 }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($proposal->status) {
                                        'Awarded' => 'success',
                                        'Open' => 'primary',
                                        'Closed' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">
                                    {{ $proposal->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('proposals.edit')
                                        <a href="{{ route('proposals.edit', $proposal->id) }}" class="btn btn-info btn-xs" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endcan
                                    @can('proposals.delete')
                                        <form action="{{ route('proposals.destroy', $proposal->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">File Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="previewFrame" src="" style="width:100%; height:80vh; border:none;"></iframe>
                    <div id="previewImageContainer" class="text-center p-3" style="display:none;">
                        <img id="previewImage" src="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).on('click', '.preview-btn', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var ext = url.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png', 'gif', 'svg'].indexOf(ext) !== -1) {
                $('#previewFrame').hide();
                $('#previewImage').attr('src', url);
                $('#previewImageContainer').show();
            } else {
                $('#previewImageContainer').hide();
                $('#previewFrame').attr('src', url).show();
            }
            $('#previewModal').modal('show');
        });
    </script>
@endpush

@include('partials.datatables-init')
