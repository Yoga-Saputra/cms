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
                        <th>Title</th>
                        <th>Description</th>
                        <th>Content</th>
                        <th>Image</th>
                        <th>Published At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $item => $post)
                        <tr>
                            <td>{{ $item + 1 }}</td>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->description }}</td>
                            <td>{{ $post->content }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $post->image) }}" class="rounded-square" width="100"
                                    height="100" alt="">
                            </td>
                            <td>{{ \Carbon\Carbon::parse($post->published_at)->format('Y-m-d') }}</td>
                            <td>
                                @if (!$post->trashed())
                                    <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-info btn-sm">Edit</a>
                                @endif
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="handleDelete({{ $post->id }}, {{ $post->trashed() }})">
                                    {{ $post->trashed() ? 'Delete' : 'Trash' }}
                                </button>

                                @if ($post->trashed())
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="handleRestore({{ $post->id }}, {{ $post->trashed() }})">
                                        Restore
                                    </button>
                                @endif
                            </td>
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
        handleRestore = (id, restore) => {
            const urlRestore = "{{ route('posts.index') }}" + '/' + 'restore-trashed-post' + '/' + id
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Restore Post',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed == true) {
                    $.ajax({
                        url: urlRestore,
                        type: "PUT",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: id,
                        success: function(data, status, xhr) {
                            Swal.fire(
                                'Success!',
                                data.message,
                                'success'
                            )
                            setTimeout(() => {
                                window.location.href =
                                    '{{ route('trashed-post.index') }}';
                            }, 500);
                        },

                        error: function(jqXhr, textStatus, errorMessage) {
                            var values = '';
                            jQuery.each(jqXhr.responseJSON.errors, function(key, value) {
                                values += value
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: values,
                            })
                        }
                    });
                }
            })
        }

        handleDelete = (id, trashed) => {
            var url = "{{ route('posts.index') }}" + '/' + id
            console.log(url);
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Hapus Post',
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
                                const trash = trashed
                                setTimeout(() => {
                                    window.location.href = trash ?
                                        '{{ route('trashed-post.index') }}' :
                                        '{{ route('posts.index') }}';
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
