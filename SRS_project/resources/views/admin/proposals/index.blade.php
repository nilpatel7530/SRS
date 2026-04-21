@extends('adminlte::page')

@section('title', 'Project Proposals')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Project Proposals</h1>
        <a href="{{ route('proposals.create') }}" class="btn btn-primary btn-sm">Create Proposal</a>
    </div>
@endsection

@section('content')
    @include('partials.alerts')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Customer / Project</th>
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
                    @forelse($proposals as $proposal)
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
                                    <a href="{{ route('documents.download', $doc->id) }}" title="Proposal {{ $index + 1 }}" class="btn btn-sm btn-outline-secondary p-1" style="font-size: 10px;">
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
                                    <a href="{{ route('proposals.edit', $proposal->id) }}" class="btn btn-info btn-xs" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('proposals.destroy', $proposal->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No proposals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
