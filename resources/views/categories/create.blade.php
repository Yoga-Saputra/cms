@extends('layouts.app')

@section('content')
    <div class="card card-default">
        <div class="card-header">
            {{ isset($category) ? 'Edit Category' : 'Create Category' }}
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
                @if (isset($category))
                    @csrf
                    @method('PUT')
                @endif
                <div class="form-group">
                    <input type="hidden" id="id" value="{{ isset($category) ? $category->id : '' }}">
                    <label for="name" class="control-label"></label>
                    <input type="text" name="name" id="name"
                        value="{{ isset($category) ? $category->name : '' }}" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Submit' }}</button>
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
                            url: "{{ route('categories.store') }}",
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
                                        '{{ route('categories.index') }}';
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
                            url: "{{ route('categories.index') }}" + '/' + id,
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
                                        '{{ route('categories.index') }}';
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
