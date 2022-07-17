@push('src-styles')

@endpush

@push('src-scripts')
<script src="{{ asset('assets-back/js/plugins/amchart/core.js') }}"></script>
<script src="{{ asset('assets-back/js/plugins/amchart/chart.js') }}"></script>
<script src="{{ asset('assets-back/js/plugins/amchart/animated.js') }}"></script>
{{-- <script src="//www.amcharts.com/lib/4/themes/material.js"></script> --}}
<script src="https://cdn.amcharts.com/lib/4/themes/kelly.js"></script>
@endpush
