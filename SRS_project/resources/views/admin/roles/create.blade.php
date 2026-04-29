@extends('adminlte::page')

@section('title', 'Create Role')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-0">
        <h1 class="text-dark font-weight-bold" style="font-size: 1.5rem;">Create New <span class="text-primary">Role</span></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0 small">
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    @include('partials.alerts')
    
    <div class="card lumina-card">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body p-3">
                <!-- Compact Floating Label Input -->
                <div class="lumina-form-group mb-4">
                    <input type="text" class="lumina-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required placeholder=" ">
                    <label for="name" class="lumina-label">Role Name</label>
                    <div class="lumina-focus-bar"></div>
                    @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary-soft p-1 rounded mr-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-lock text-primary small"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-weight-bold text-dark">Initial Permissions</h6>
                        <p class="text-muted extra-small mb-0 text-truncate">Assign default capabilities to this new role.</p>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                <i class="fas fa-shield-alt text-primary mr-2"></i> Permissions Matrix
                            </h6>
                            <div class="btn-group">
                                <button type="button" class="btn btn-xs btn-outline-primary select-all-global mr-2">Select All</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary deselect-all-global">Deselect All</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 permission-matrix">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 200px;" class="py-3 px-4">Module / Page</th>
                                        <th class="text-center py-3">Access</th>
                                        <th class="text-center py-3">View</th>
                                        <th class="text-center py-3">Create</th>
                                        <th class="text-center py-3">Edit</th>
                                        <th class="text-center py-3">Delete</th>
                                        <th class="text-center py-3">Others</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $standardActions = ['access', 'view', 'create', 'edit', 'delete'];
                                        $grouped = $permissions->groupBy(function($perm) {
                                            return explode('.', $perm->name)[0];
                                        });
                                    @endphp

                                    @foreach($grouped as $module => $modulePermissions)
                                        <tr>
                                            <td class="font-weight-bold text-capitalize px-4">
                                                <i class="fas {{ $icons[$module] ?? 'fa-cube' }} fa-fw mr-2 text-muted small"></i>
                                                {{ $module }}
                                            </td>
                                            @foreach($standardActions as $action)
                                                <td class="text-center">
                                                    @php
                                                        $perm = $modulePermissions->first(function($p) use ($module, $action) {
                                                            return $p->name === "$module.$action";
                                                        });
                                                    @endphp
                                                    @if($perm)
                                                        <div class="custom-control custom-checkbox lumina-checkbox d-inline-block">
                                                            <input type="checkbox" class="custom-control-input" 
                                                                   name="permissions[]" id="perm-{{ $perm->id }}" 
                                                                   value="{{ $perm->name }}">
                                                            <label class="custom-control-label" for="perm-{{ $perm->id }}"></label>
                                                        </div>
                                                    @else
                                                        <span class="text-muted opacity-25">&mdash;</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center">
                                                @php
                                                    $otherPerms = $modulePermissions->filter(function($p) use ($module, $standardActions) {
                                                        $action = str_replace($module . '.', '', $p->name);
                                                        return !in_array($action, $standardActions);
                                                    });
                                                @endphp
                                                @foreach($otherPerms as $perm)
                                                    <div class="d-block mb-1 text-left px-2">
                                                        <div class="custom-control custom-checkbox lumina-checkbox d-inline-block">
                                                            <input type="checkbox" class="custom-control-input" 
                                                                   name="permissions[]" id="perm-{{ $perm->id }}" 
                                                                   value="{{ $perm->name }}">
                                                            <label class="custom-control-label" for="perm-{{ $perm->id }}">
                                                                <small class="text-muted ml-1">{{ strtoupper(str_replace($module . '.', '', $perm->name)) }}</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($otherPerms->isEmpty())
                                                    <span class="text-muted opacity-25">&mdash;</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white p-3 text-right border-top-0">
                <a href="{{ route('roles.index') }}" class="btn btn-link text-muted mr-3 small">Cancel</a>
                <button type="submit" class="btn lumina-btn-primary px-4 shadow-sm py-2 small">
                    Create Role
                </button>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        :root {
            --lumina-primary: #0056D2;
            --lumina-primary-hover: #0048b3;
            --lumina-primary-soft: rgba(0, 86, 210, 0.1);
            --lumina-bg: #f5f7f9;
            --lumina-surface: #ffffff;
            --lumina-border: #e2e8f0;
            --lumina-text-dark: #1e293b;
        }

        .extra-small { font-size: 0.75rem; }

        .lumina-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
            background: var(--lumina-surface);
            overflow: hidden;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 86, 210, 0.1) !important;
            border-top: 2px solid transparent;
            transition: border-top-color 0.3s;
        }

        .permission-card {
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.03) !important;
        }

        .permission-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: rgba(0, 86, 210, 0.1) !important;
        }

        .permission-card:hover .glass-header {
            border-top-color: var(--lumina-primary);
        }

        /* Compact Floating Label Input */
        .lumina-form-group {
            position: relative;
            margin-top: 5px;
        }

        .lumina-control {
            width: 100%;
            padding: 8px 4px;
            font-size: 1rem;
            border: none;
            border-bottom: 2px solid var(--lumina-border);
            background: transparent;
            transition: border-color 0.3s;
            outline: none;
            color: var(--lumina-text-dark);
        }

        .lumina-label {
            position: absolute;
            top: 8px;
            left: 4px;
            font-size: 1rem;
            color: #94a3b8;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .lumina-control:focus ~ .lumina-label,
        .lumina-control:not(:placeholder-shown) ~ .lumina-label {
            top: -10px;
            font-size: 0.75rem;
            color: var(--lumina-primary);
            font-weight: 700;
        }

        .lumina-focus-bar {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--lumina-primary);
            transition: all 0.4s ease;
        }

        .lumina-control:focus ~ .lumina-focus-bar {
            width: 100%;
            left: 0;
        }

        /* Compact Switch Styling */
        .lumina-switch.custom-switch {
            padding-left: 1.75rem;
        }
        
        .lumina-switch .custom-control-label::before {
            height: 0.9rem;
            width: 1.5rem;
            border-radius: 0.5rem;
            left: -1.75rem;
            background-color: #cbd5e1;
            border-color: #cbd5e1;
        }
        
        .lumina-switch .custom-control-label::after {
            width: calc(0.9rem - 4px);
            height: calc(0.9rem - 4px);
            left: calc(-1.75rem + 2px);
            border-radius: 0.5rem;
        }
        
        .lumina-switch .custom-control-input:checked ~ .custom-control-label::after {
            transform: translateX(0.6rem);
        }

        .lumina-switch .custom-control-input:checked ~ .custom-control-label::before {
            border-color: var(--lumina-primary);
            background-color: var(--lumina-primary);
        }

        .lumina-switch .custom-control-label {
            cursor: pointer;
            line-height: 1.2;
            padding-top: 2px;
        }

        /* Buttons */
        .lumina-btn-primary {
            background: linear-gradient(135deg, var(--lumina-primary) 0%, #3084ff 100%);
            border: none;
            color: white;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .lumina-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px -3px rgba(0, 86, 210, 0.4);
            color: white;
        }

        .lumina-btn-outline {
            border: 1px solid var(--lumina-border);
            color: #64748b;
            border-radius: 4px;
            padding: 1px 6px;
            transition: all 0.2s;
        }

        .lumina-btn-outline:hover {
            background: var(--lumina-primary-soft);
            color: var(--lumina-primary);
            border-color: var(--lumina-primary);
        }

        .bg-primary-soft {
            background-color: var(--lumina-primary-soft);
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select-all').on('click', function() {
                var target = $('#' + $(this).data('target'));
                var checkboxes = target.find('input[type="checkbox"]');
                var allChecked = true;
                
                checkboxes.each(function() {
                    if (!$(this).prop('checked')) allChecked = false;
                });
                
                checkboxes.prop('checked', !allChecked);
                $(this).text(!allChecked ? 'Deselect All' : 'Select All');
            });
        });
    </script>
@stop
