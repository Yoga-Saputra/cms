@extends('layouts.app')

@section('content')
    <div class="card card-default">
        <div class="card-header">
            {{ isset($tag) ? 'Edit Tag' : 'Create Tag' }}
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
                @if (isset($tag))
                    @csrf
                    @method('PUT')
                @endif
                <div class="form-group">
                    <input type="hidden" id="id" value="{{ isset($tag) ? $tag->id : '' }}">
                    <label for="name" class="control-label"></label>
                    <input id="name" type="hidden" name="name">
                    <trix-editor input="name"></trix-editor>
                </div>

                <div class="form-group">
                    <button type="submit"
                        class="btn btn-primary">{{ isset($tag) ? 'Update Tag' : 'Submit Tag' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#form-item').on('submit', function(e) {
                console.log('tes');
                if (!e.preventDefault()) {
                    var id = $('#id').val();
                    var data = $('#form-item').serializeArray();
                    if (!id)
                        $.ajax({
                            url: "{{ route('tags.store') }}",
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
                                        '{{ route('tags.index') }}';
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
                    else
                        $.ajax({
                            url: "{{ route('tags.index') }}" + '/' + id,
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
                                        '{{ route('tags.index') }}';
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
