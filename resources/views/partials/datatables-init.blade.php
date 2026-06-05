@push('css')
<style>
/* ─── Premium Export Buttons ─── */
.srs-export-bar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    margin-bottom: 15px;
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}
.srs-export-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    margin-right: auto;
}
.srs-btn-export {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 6px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none !important;
    color: #fff !important;
    line-height: 1.5;
}
.srs-btn-export:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    color: #fff !important;
}
.srs-btn-export:active { transform: translateY(0); }
.srs-btn-csv   { background-color: #17a2b8; border-color: #17a2b8; }
.srs-btn-csv:hover { background-color: #138496; border-color: #117a8b; }
.srs-btn-excel { background-color: #28a745; border-color: #28a745; }
.srs-btn-excel:hover { background-color: #218838; border-color: #1e7e34; }
.srs-btn-print { background-color: #6c757d; border-color: #6c757d; }
.srs-btn-print:hover { background-color: #5a6268; border-color: #545b62; }

/* Hide default DataTables dom buttons */
div.dt-buttons { display: none !important; }

/* Search box */
.dataTables_wrapper .dataTables_filter input {
    border-radius: 6px !important;
    border: 1px solid #dee2e6 !important;
    padding: 4px 10px !important;
    font-size: 0.83rem !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.15) !important;
    outline: none !important;
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    padding-top: 8px;
}
</style>
@endpush

@push('js')
<script>
(function() {
    $(document).ready(function() {

        @php
            $tableSelector = isset($id) ? '#' . $id : '.datatable';
            $showExports   = !isset($hideExports) || !$hideExports;
            $uniqueKey     = isset($id) ? $id : 'dt_' . mt_rand(1000,9999);
        @endphp

        var dtOptions = {
            responsive: {{ isset($responsive) && $responsive === false ? 'false' : 'true' }},
            autoWidth: false,
            @if(isset($ajaxUrl))
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ $ajaxUrl ?? '' }}",
                @if(isset($ajaxData))
                data: {!! $ajaxData !!}
                @endif
            },
            columns: {!! json_encode($columns ?? []) !!},
            @endif

            @if($showExports)
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csvHtml5',
                  text: '',
                  className: 'srs-btn-export srs-btn-csv _srs-csv-{{ $uniqueKey }}',
                  title: document.title
                },
                { extend: 'excelHtml5',
                  text: '',
                  className: 'srs-btn-export srs-btn-excel _srs-excel-{{ $uniqueKey }}',
                  title: document.title
                }
            ],
            @else
            dom: 'frtip',
            @endif

            language: {
                searchPlaceholder: "Quick search...",
                search: "",
                processing: '<span class="spinner-border spinner-border-sm mr-1" role="status"></span> Loading…',
                emptyTable: '<div class="text-center py-3 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No records found</div>',
                zeroRecords: '<div class="text-center py-3 text-muted"><i class="fas fa-search fa-2x mb-2 d-block"></i>No matching records</div>'
            }
        };

        var table;
        try {
            table = $('{{ $tableSelector }}').DataTable(dtOptions);
            console.log("DataTables initialized for {{ $tableSelector }}");
        } catch (e) {
            console.error("DataTables initialization failed:", e);
        }

        // Style search box
        $('.dataTables_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Search records...');

        @if($showExports)
        // ── Build our custom export bar ──
        var $dtWrapper = $('{{ $tableSelector }}').closest('.dataTables_wrapper');
        var $target = $dtWrapper.length ? $dtWrapper : $('{{ $tableSelector }}').parent();
        
        // If table is inside a responsive container, we want the bar ABOVE that container
        var $responsiveParent = $('{{ $tableSelector }}').closest('.table-responsive');
        if ($responsiveParent.length) {
            $target = $responsiveParent;
        }

        var exportBarId = "srs-export-{{ $uniqueKey }}";
        $('#' + exportBarId).remove();

        var exportBar = $('<div class="srs-export-bar" id="' + exportBarId + '">' +
            '<span class="srs-export-label"><i class="fas fa-download mr-1"></i>Report Exports</span>' +
            '<div class="d-flex gap-2">' +
                '<button type="button" class="srs-btn-export srs-btn-csv" id="srs-csv-{{ $uniqueKey }}">' +
                    '<i class="fas fa-file-csv"></i> CSV' +
                '</button>' +
                '<button type="button" class="srs-btn-export srs-btn-excel" id="srs-excel-{{ $uniqueKey }}">' +
                    '<i class="fas fa-file-excel"></i> Excel' +
                '</button>' +
                '<button type="button" class="srs-btn-export srs-btn-print" id="srs-print-{{ $uniqueKey }}">' +
                    '<i class="fas fa-print"></i> Print' +
                '</button>' +
            '</div>' +
        '</div>');

        $target.before(exportBar);

        // Map custom buttons to hidden DataTables buttons
        $('#srs-csv-{{ $uniqueKey }}').on('click', function(e) {
            e.preventDefault();
            console.log("Exporting CSV...");
            var btn = table.button('._srs-csv-{{ $uniqueKey }}');
            if (btn && btn.node()) {
                $(btn.node()).click();
            } else {
                console.error("CSV button not found in DataTables.");
            }
        });
        $('#srs-excel-{{ $uniqueKey }}').on('click', function(e) {
            e.preventDefault();
            console.log("Exporting Excel...");
            var btn = table.button('._srs-excel-{{ $uniqueKey }}');
            if (btn && btn.node()) {
                $(btn.node()).click();
            } else {
                console.error("Excel button not found in DataTables.");
            }
        });
        $('#srs-print-{{ $uniqueKey }}').on('click', function(e) {
            e.preventDefault();
            window.print();
        });
        @endif

    });
})();
</script>
@endpush
