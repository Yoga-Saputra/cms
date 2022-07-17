@extends('layouts.app')

@section('content')
    <div class="card card-default">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex justify-content-start">
                        <h4>Categories</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('categories.create') }}" class="btn btn-success">Add Category</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-checkable" id="post_datatable">
                <thead>
                    <tr class="table-info">
                        <th>No</th>
                        <th>Name</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $item => $category)
                        <tr>
                            <td>{{ $item + 1 }}</td>
                            <td>{!! $category->name !!}</td>
                            <td>
                                <a href="{{ route('categories.edit', $category->id) }}"
                                    class="btn btn-info btn-sm">Edit</a>

                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="handleDelete({{ $category->id }})">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        handleDelete = (id) => {
            var url = "{{ route('categories.index') }}" + '/' + id
            console.log(url);
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Hapus Kategori',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed == true) {
                    var csrf_token = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        data: {
                            '_token': csrf_token
                        },
                        success: function(data, status, xhr) {
                            if (data.success === true) {
                                Swal.fire(
                                    'Deleted!',
                                    data.message,
                                    'success'
                                )
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('categories.index') }}';
                                }, 500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                })
                            }
                        },
                        error: function(jqXhr, textStatus, errorMessage) {
                            console.log(textStatus);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: jqXhr.responseJSON.message,
                            })
                        }
                    })
                }
            })

        }
    </script>
@endsection
