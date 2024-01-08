@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_{{ $section }}">

        <div class="panel-heading">
            <div class="panel-title"><i class="icon logs"></i> {{ trans('admin.'.$section) }}</div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.'.ucfirst($section).'.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_{{ $section }}', {
        url:'{{ route("admin.{$section}.index") }}',
        delete_url:'{{ route("admin.{$section}.destroy") }}'
    });
</script>
@stop