@extends('adminlte::page')

@section('title', 'System Branding')

@section('content_header')
    <h1>System Branding</h1>
@stop

@section('content')
    @include('partials.alerts')

    @if(!file_exists(public_path('storage')))
        <div class="alert alert-warning alert-dismissible shadow-sm">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Action Required!</h5>
            <p>The storage symbolic link appears to be missing on the server. If logos are not appearing, click the button below to fix it programmatically.</p>
            <form action="{{ route('admin.branding.fixStorage') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-dark btn-sm">
                    <i class="fas fa-link mr-1"></i> Fix Link Now
                </button>
            </form>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">General Branding</h3>
                </div>
                <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="app_name">Application Name</label>
                            <input type="text" name="app_name" id="app_name" class="form-control @error('app_name') is-invalid @enderror" 
                                   value="{{ old('app_name', $settings['app_name']) }}" required>
                            @error('app_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="text-muted">This name appears in the browser tab and sidebar.</small>
                        </div>

                        <div class="form-group">
                            <label for="logo_img">Application Logo</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('logo_img') is-invalid @enderror" id="logo_img" name="logo_img">
                                    <label class="custom-file-label" for="logo_img">Choose file</label>
                                </div>
                            </div>
                            @error('logo_img') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div class="mt-3">
                                <label>Current Logo Preview:</label><br>
                                <img src="{{ asset($settings['app_logo']) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px; background-color: #343a40;">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Branding</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Live Preview Information</h3>
                </div>
                <div class="card-body text-center py-5">
                    <h2 class="mb-4">"{{ $settings['app_name'] }}"</h2>
                    <img src="{{ asset($settings['app_logo']) }}" alt="Preview Logo" class="elevation-3 img-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    <p class="mt-4 text-muted">A dynamic branding system ensures your identity is consistent throughout the ERP suite.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@stop
