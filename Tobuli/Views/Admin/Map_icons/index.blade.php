@extends('Admin.Layouts.default')

@section('styles')
    <link href="{{ asset('assets/plugins/dropzone/css/dropzone.css') }}" rel="stylesheet"/>
@stop

@section('content')
    <div class="panel panel-default" id="table_{{ $section }}">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <div class="panel-title"><i class="icon user"></i> {{ trans('admin.'.$section) }}</div>
        </div>

        <div class="panel-body">
            <form action="{{ route("admin.{$section}.store") }}" class="dropzone" id="my-dropzone" style="min-height: 150px;"></form>

            <div data-table>
                @include('Admin.'.ucfirst($section).'.table')
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
    <script>
        tables.set_config('table_{{ $section }}', {
            url: '{{ route("admin.{$section}.index") }}',
            delete_url:'{{ route("admin.{$section}.destroy") }}'
        });
        $(document).ready(function() {
            Dropzone.options.myDropzone = {
                init: function() {
                    this.on("queuecomplete", function() {
                        tables.get('table_{{ $section }}');
                    });
                }
            };

            $(document).on('click', '.table-icon .controls a', function () {
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.{$section}.destroy") }}',
                    data: {
                        _method: 'DELETE',
                        id: {0:$(this).data('id')}
                    },
                    success: function () {
                        tables.get('table_{{ $section }}');
                    }
                });
            });
        });
    </script>
@stop