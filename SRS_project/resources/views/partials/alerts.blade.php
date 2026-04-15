@if(session('success'))
    <div class="alert alert-success alert-dismissible auto-dismiss">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible auto-dismiss">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {{ session('error') }}
    </div>
@endif

@push('js')
<script>
    $(document).ready(function() {
        // Auto-dismiss alerts after 3.5 seconds
        setTimeout(function() {
            $(".alert.auto-dismiss").fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3500);
    });
</script>
@endpush
