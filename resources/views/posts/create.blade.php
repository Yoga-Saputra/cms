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


            <form id="form-item1" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($post))
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
                    <input id="description" value="{!! isset($post) ? $post->description : '' !!}" type="hidden" name="description">
                    <trix-editor input="description"></trix-editor>
                </div>

                <div class="form-group">
                    <label for="content" class="control-label">Content</label>
                    <input id="content" value="{!! isset($post) ? $post->content : '' !!}" type="hidden" name="content">
                    <trix-editor input="content"></trix-editor>
                </div>

                <div class="form-group">
                    <label for="published_at" class="control-label">Published At</label>
                    <input type="date" name="published_at"
                        value="{{ isset($post) ? \Carbon\Carbon::parse($post->published_at)->format('Y-m-d') : '' }}"
                        id="published_at"class="form-control">
                </div>

                <div class="form-group">
                    <label for="category" class="control-label">Category</label>
                    <select name="category" id="category" class="form-control">
                        @foreach ($categories as $cat)
                            @if (isset($post))
                                <option value="{{ $cat->id }}" @if ($cat->id == old('category_id', $post->category_id)) selected @endif>
                                    {!! $cat->name !!}
                                </option>
                            @else
                                <option value="{{ $cat->id }}">
                                    {!! $cat->name !!}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                @if ($tags->count() > 0)
                    <div class="form-group">
                        <label for="tags" class="control-label">Tag</label>
                        <select name="tags[]" id="tags" class="form-control js-example-basic-single tag-selector"
                            multiple>
                            @foreach ($tags as $tag)
                                @if (isset($post))
                                    <option value="{{ $tag->id }}" @if ($post->hasTag($tag->id)) selected @endif>
                                        {!! $tag->name !!}
                                    </option>
                                @else
                                    <option value="{{ $tag->id }}">
                                        {!! $tag->name !!}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endif

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
        $('.js-example-basic-single').select2();

        flatpickr('#published_at', {
            enableTime: true
        })

        $(document).ready(function() {
            $('#form-item1').on('submit', function(e) {
                if (!e.preventDefault()) {
                    var id = $('#id').val();
                    if (!id)
                        $.ajax({
                            url: "{{ route('posts.store') }}",
                            type: "POST",
                            data: new FormData($("#form-item1")[0]),
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                if (data.status == true) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: '5000'
                                    })
                                    setTimeout(() => {
                                        window.location.href =
                                            '{{ route('posts.index') }}';
                                    }, 500);
                                } else {
                                    Swal.fire({
                                        title: 'Ops!',
                                        icon: 'error',
                                        text: data.message,
                                        timer: '5000'
                                    })

                                    setTimeout(() => {
                                        window.location.href =
                                            '{{ route('categories.index') }}';
                                    }, 1500);
                                }
                            },
                            error: function(jqXhr, textStatus, errorMessage) {
                                var values = '';
                                jQuery.each(jqXhr.responseJSON.errors, function(key, value) {
                                    values += "<span style='color:red'> " + value +
                                        "</span>" + "<br>"
                                });

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    html: values,
                                    timer: '1500'
                                })
                            }
                        });
                    else
                        $.ajax({
                            url: "{{ route('posts.index') }}" + '/' + id,
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: new FormData($("#form-item1")[0]),
                            contentType: false,
                            processData: false,
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
                                    values += "<span style='color:red'> " + value +
                                        "</span>" + "<br>"
                                });

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    html: values
                                })
                            }
                        });
                    return false;
                }
            });
        });
    </script>
@endsection
