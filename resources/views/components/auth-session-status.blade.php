@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success alert-dismissible fade show small py-2 px-3 mb-3']) }} role="alert">
        {{ $status }}
        <button type="button" class="close py-2" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
