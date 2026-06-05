@if(session('success'))
    <div class="alert alert-success alert-dismissible auto-dismiss">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-check"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible auto-dismiss">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fas fa-ban"></i> {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors</h5>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@push('js')
<script>
    $(document).ready(function() {
        // Auto-dismiss success/error alerts after 3.5 seconds
        // But NOT validation errors as they require user attention
        setTimeout(function() {
            $(".alert.auto-dismiss").fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3500);
    });
</script>
@endpush
