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
                        <th>ID</th>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Submission Date</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $proposal)
                        <tr>
                            <td>{{ $proposal->id }}</td>
                            <td>{{ $proposal->name }}</td>
                            <td>{{ number_format($proposal->estimated_value, 2) }}</td>
                            <td>{{ $proposal->submission_date }}</td>
                            <td>
                                @php
                                    $badgeClass = match($proposal->status) {
                                        'Approved' => 'success',
                                        'Pending' => 'warning',
                                        'Rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">
                                    {{ $proposal->status }}
                                </span>
                            </td>
                            <td>{{ $proposal->creator->name ?? 'System' }}</td>
                            <td>
                                <a href="{{ route('proposals.edit', $proposal->id) }}" class="btn btn-info btn-xs">Edit</a>
                                <form action="{{ route('proposals.destroy', $proposal->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this proposal?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No proposals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
