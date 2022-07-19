@extends('layouts.app')

@section('content')
    <div class="card card-default">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex justify-content-start">
                        <h4>Posts</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('posts.create') }}" class="btn btn-success">Add Post</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-checkable" id="post_datatable">
                <thead>
                    <tr class="table-info">
                        <th>No</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        @if (!auth()->user()->isAdmin())
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $item => $user)
                        <tr>
                            <td>{{ $item + 1 }}</td>
                            <td>
                                <img width="50px" height="50px" style="border-radius: 50%"
                                    src="{{ Gravatar::src($user->email) }}" alt="" srcset="">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}</td>
                            @if (!$user->isAdmin())
                                <td>
                                    {{-- <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm">Edit</a> --}}
                                    <button id="updateAdmin" type="button" data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}" class="btn btn-info btn-sm">Make
                                        Admin
                                    </button>

                                    {{-- <button type="button" class="btn btn-danger btn-sm"
                                    onclick="handleDelete({{ $user->id }})">Delete
                                </button> --}}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan=7 class="text-center">
                                <span class="text-muted">No data.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#updateAdmin').click(function(e) {
            let name = $(this).data('name');
            let id = $(this).data('id');
            var url = "{{ route('users') }}" + '/' + id + '/' + 'make-admin'

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Menjadikan user "' + name + '" menjadi admin',
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
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': csrf_token,
                            'id': id
                        },
                        success: function(data, status, xhr) {
                            console.log(status);
                            if (data.status === true) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: '5000'
                                })
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('users') }}';
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
        })
        $(document).ready(function() {})
    </script>
@endsection
