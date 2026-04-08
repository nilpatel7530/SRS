@extends('adminlte::page')

@section('title', 'Reports')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>System Reports</h1>
        <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-file-csv"></i> Export to CSV
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Consolidated Project Report</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Department</th>
                        <th>Total CAPEX</th>
                        <th>Total OPEX</th>
                        <th>BG Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportData as $row)
                        <tr>
                            <td>{{ $row['project_name'] }}</td>
                            <td>{{ $row['department'] }}</td>
                            <td>${{ number_format($row['total_capex'], 2) }}</td>
                            <td>${{ number_format($row['total_opex'], 2) }}</td>
                            <td>
                                @if($row['bg_status'] == 'Active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Missing</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No projects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
