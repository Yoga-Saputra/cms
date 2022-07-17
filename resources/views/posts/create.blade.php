@extends('layouts.app')

@section('content')
    <div class="card card-default">
        <div class="card-header">
            {{ isset($post) ? 'Edit post' : 'Create post' }}
        </div>
        <div class="card-body">
            @if ($errors->any())
                @php
                    $error_text = '';

                    foreach ($errors->all() as $error) {
                        $error_text = $error_text . $error . '</br>';
                    }
                @endphp
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: {!! json_encode($error_text) !!}
                    })
                </script>
            @endif


            <form id="form-item" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($post))
                    @csrf
                    @method('PUT')
                @endif
                <input type="hidden" id="id" value="{{ isset($post) ? $post->id : '' }}">

                <div class="form-group">
                    <label for="title" class="control-label">Title</label>
                    <input type="text" name="title" id="title" value="{{ isset($post) ? $post->title : '' }}"
                        class="form-control">
                </div>

                <div class="form-group">
                    <label for="description" class="control-label">Description</label>
                    <textarea name="description" id="description" class="form-control" cols="30" rows="10">{{ isset($post) ? $post->description : '' }}</textarea>
                </div>

                <div class="form-group">
                    <label for="content" class="control-label">Content</label>
                    <textarea name="content" id="content" class="form-control" cols="30" rows="10">{{ isset($post) ? $post->content : '' }}</textarea>
                </div>

                <div class="form-group">
                    <label for="published_at" class="control-label">Published At</label>
                    <input type="date" name="published_at" id="published_at"
                        value="{{ isset($post) ? \Carbon\Carbon::parse($post->published_at)->format('Y-m-d') : '' }}"
                        class="form-control">
                </div>

                <div class="form-group">
                    <label for="image" class="control-label">Image</label>
                    @if (isset($post))
                        <div class="mt-3 mb-3">
                            <img src="{{ asset('storage/' . $post->image) }}" class="rounded-square" width="200"
                                height="200" alt="">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#form-item').on('submit', function(e) {
                if (!e.preventDefault()) {
                    var id = $('#id').val();
                    var data = $('#form-item').serializeArray();
                    if (!id)
                        $.ajax({
                            url: "{{ route('posts.store') }}",
                            type: "POST",
                            data: new FormData($("#form-item")[0]),
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    type: 'success',
                                    timer: '5000'
                                })
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('posts.index') }}';
                                }, 500);
                            },
                            error: function(data) {
                                Swal.fire({
                                    title: 'Oops...',
                                    text: data.message,
                                    type: 'error',
                                    timer: '1500'
                                })
                            }
                        });
                    else
                        $.ajax({
                            url: "{{ route('posts.index') }}" + '/' + id,
                            type: "PUT",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: data,
                            success: function(data, status, xhr) {
                                Swal.fire(
                                    'Success!',
                                    data.message,
                                    'success'
                                )
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('posts.index') }}';
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
                    return false;
                }
            });
        });
    </script>
@endsection
