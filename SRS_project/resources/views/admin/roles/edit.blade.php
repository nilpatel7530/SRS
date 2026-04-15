@extends('adminlte::page')

@section('title', 'Edit Role')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-0">
        <h1 class="text-dark font-weight-bold" style="font-size: 1.5rem;">Edit Role: <span class="text-primary">{{ $role->name }}</span></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0 small">
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    @include('partials.alerts')
    
    <div class="card lumina-card">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body p-3">
                <!-- Compact Floating Label Input -->
                <div class="lumina-form-group mb-4">
                    <input type="text" class="lumina-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $role->name) }}" required placeholder=" ">
                    <label for="name" class="lumina-label">Role Name</label>
                    <div class="lumina-focus-bar"></div>
                    @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary-soft p-1 rounded mr-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-key text-primary small"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-weight-bold text-dark">Access Control & Permissions</h6>
                        <p class="text-muted extra-small mb-0 text-truncate">Define granular page access for this role.</p>
                    </div>
                </div>

                <div class="row mx-n2">
                    @php
                        $groupedPermissions = $permissions->groupBy(function($perm) {
                            return explode('.', $perm->name)[0];
                        });
                        
                        $icons = [
                            'dashboard' => 'fa-tachometer-alt',
                            'projects' => 'fa-project-diagram',
                            'proposals' => 'fa-file-contract',
                            'reports' => 'fa-chart-bar',
                            'departments' => 'fa-building',
                            'users' => 'fa-users',
                            'roles' => 'fa-user-tag',
                            'permissions' => 'fa-key',
                            'administration' => 'fa-cogs',
                            'manage-all-settings' => 'fa-user-shield'
                        ];

                        // Column Stacking Strategy
                        $numCols = 4;
                        $cols = array_fill(0, $numCols, []);
                        $i = 0;
                        foreach($groupedPermissions as $group => $groupPerms) {
                            $cols[$i % $numCols][$group] = $groupPerms;
                            $i++;
                        }
                    @endphp

                    @foreach($cols as $colIdx => $columnGroups)
                        <div class="col-xl-3 col-lg-6 col-md-6 px-2">
                            @foreach($columnGroups as $group => $groupPermissions)
                                <div class="card permission-card shadow-sm border-0 mb-3">
                                    <div class="card-header glass-header py-2 px-3 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas {{ $icons[$group] ?? 'fa-cube' }} fa-fw mr-2 text-primary opacity-75"></i>
                                            <h6 class="mb-0 text-capitalize font-weight-bold text-dark small">{{ $group }}</h6>
                                        </div>
                                        @if(count($groupPermissions) > 1)
                                            <button type="button" class="btn btn-xs lumina-btn-outline select-all" data-target="{{ $group }}-group" style="padding: 0 4px; font-size: 0.65rem;">
                                                All
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body py-2 px-3" id="{{ $group }}-group">
                                        @foreach($groupPermissions as $permission)
                                            <div class="custom-control custom-switch lumina-switch mb-1">
                                                <input class="custom-control-input" type="checkbox" name="permissions[]" 
                                                       id="perm-{{ $permission->id }}" value="{{ $permission->name }}"
                                                       {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="perm-{{ $permission->id }}">
                                                    <span class="text-dark extra-small font-weight-500">
                                                        {{ str_replace(['.', '_'], ' ', str_replace($group . '.', '', $permission->name)) }}
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer bg-white p-3 text-right border-top-0">
                <a href="{{ route('roles.index') }}" class="btn btn-link text-muted mr-3 small">Discard</a>
                <button type="submit" class="btn lumina-btn-primary px-4 shadow-sm py-2 small">
                    Update Role
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
