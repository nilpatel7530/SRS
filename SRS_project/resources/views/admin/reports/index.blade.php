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
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Types (F/P)</th>
                        <th>Total CAPEX</th>
                        <th>Total OPEX</th>
                        <th>Invoiced</th>
                        @foreach(array_keys(current($reportData)['month_wise'] ?? []) as $month)
                            <th class="bg-light">{{ $month }}</th>
                        @endforeach
                        <th class="bg-info">Projection</th>
                        <th>BG Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportData as $row)
                        <tr>
                            <td><strong>{{ $row['project_name'] }}</strong><br><small>{{ $row['department'] }}</small></td>
                            <td>{{ $row['financial_type'] }}<br>{{ $row['project_type'] }}</td>
                            <td>{{ number_format($row['total_capex'], 2) }}</td>
                            <td>{{ number_format($row['total_opex'], 2) }}</td>
                            <td>{{ number_format($row['total_invoiced'], 2) }}</td>
                            @foreach($row['month_wise'] as $total)
                                <td class="bg-light">{{ $total > 0 ? number_format($total, 0) : '-' }}</td>
                            @endforeach
                            <td class="bg-info">{{ number_format(array_sum($row['projections']), 0) }}</td>
                            <td>
                                <span class="badge {{ $row['bg_status'] == 'Active' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $row['bg_status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No projects found for the selected criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
