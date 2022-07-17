@push('src-styles')
    <!-- Datatables -->
    <link rel="stylesheet" href="{{ asset('assets-back/datatabel/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-back/datatabel/dataTables.bootstrap4.min.css') }}">

@endpush

@push('src-scripts')
    <script src="{{ asset('assets-back/datatabel/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets-back/datatabel/dataTables.bootstrap4.min.js') }}"></script>
@endpush
