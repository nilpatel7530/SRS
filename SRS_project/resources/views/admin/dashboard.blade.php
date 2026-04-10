@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to the Project Management System.</p>
    
    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_projects'] }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <a href="{{ route('projects.index') }}" class="small-box-footer">View List <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['budget_utilization'] }}<sup style="font-size: 20px">%</sup></h3>
                    <p>Budget Utilization</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="small-box-footer">
                    Total: {{ number_format($stats['total_financials'], 0) }}
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['active_bgs'] }}</h3>
                    <p>Active BGs ({{ $stats['expiring_bgs'] }} Expiring)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="small-box-footer">
                    {{ $stats['expiring_bgs'] }} within 30 days
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['pending_invoices'] }}</h3>
                    <p>Pending Invoices</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="small-box-footer">
                    Amt: {{ number_format($stats['total_invoiced'], 0) }}
                </div>
            </div>
        </div>
        <!-- ./col -->
    </div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
