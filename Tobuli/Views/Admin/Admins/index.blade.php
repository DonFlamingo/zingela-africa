@extends('Admin.Layouts.default')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/global/plugins/data-tables/DT_bootstrap.css') }}"/>
@stop

@section('content')
<h3 class="page-title">
    {{ trans('admin.'.$section) }}
</h3>
<div class="dataTables_wrapper ajaxTable" id="table_{{ $section }}">
    <div class="filter">
        <div class="row">
            <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}">
            <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}">
            <div class="col-md-6 col-sm-6">
                <div class="btn-group">
                    <button class="btn green modal_open" data-modal="{{ $section }}_create" data-url="{{ route("admin.{$section}.create") }}">
                        {{ trans('admin.add_new') }} <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="dataTables_filter">
                    <label>{{ trans('admin.search_it') }}: <input type="text" name="search_phrase" class="form-control input-medium input-inline"></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="btn-group">
                    <button class="btn dropdown-toggle" data-toggle="dropdown">{{ trans('admin.actions') }} <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-left">
                        <li>
                            <a href="javascript:" class="multi_delete">{{ trans('admin.delete_selected') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="list">
            @include('Admin.'.ucfirst($section).'.table')
        </div>
    </div>
</div>
@stop

@section('javascript')
<script src="{{ asset('assets/admin/pages/scripts/jquery.ba-throttle-debounce.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/scripts/tables/tables.js') }}" type="text/javascript"></script>
<script>
    tables.set_config('table_{{ $section }}', {
        url:'{{ route("admin.{$section}.index") }}',
        delete_url:'{{ route("admin.{$section}.destroy") }}'
    });

    function {{ $section }}_edit_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    function {{ $section }}_create_modal_callback() {
        tables.get('table_{{ $section }}');
    }
</script>
@stop