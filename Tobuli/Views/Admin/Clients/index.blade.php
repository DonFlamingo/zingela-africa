@extends('Admin.Layouts.default')

@section('content')

    <div class="panel panel-default" id="table_{{ $section }}">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_create" data-url="{{ route("admin.{$section}.create") }}">
                        <i class="icon user-add" title="{{ trans('admin.add_new_user') }}"></i>
                    </a>
                </li>
                @if( Auth::User()->perm('devices', 'edit') )
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="devices_create" data-url="{{ route("devices.create") }}">
                        <i class="icon device-add" title="{{ trans('admin.add_new_device') }}"></i>
                    </a>
                </li>
                @endif
                @if( Auth::User()->perm('geofences', 'view') )
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_import" data-url="{{ route("admin.clients.import_geofences") }}">
                        <i class="icon geofence-import" title="{{ trans('front.import_geofences') }}"></i>
                    </a>
                </li>
                @endif
                @if( Auth::User()->perm('poi', 'view') )
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_import" data-url="{{ route("admin.clients.import_map_icon") }}">
                        <i class="icon poi-import" title="{{ trans('front.import_map_icon') }}"></i>
                    </a>
                </li>
                @endif
            </ul>

            <div class="panel-title"><i class="icon user"></i> {!! trans('admin.users') !!}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => true]) !!}
                </div>
                <div class="form-group search">
                    {!! Form::text('search_device', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_device_imei'), 'data-filter' => true]) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.'.ucfirst($section).'.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    $(document).ready(function() {
        $(document).on('change', 'select[name="group_id"]', showHideClientFields);

        $(document).on('change', 'input[name="enable_devices_limit"]', function() {
            if ($(this).prop('checked'))
                $('input[name="devices_limit"]').removeAttr('disabled');
            else
                $('input[name="devices_limit"]').attr('disabled', 'disabled');
        });

        $(document).on('change', 'input[name="enable_expiration_date"]', function() {
            if ($(this).prop('checked'))
                $('input[name="expiration_date"]').removeAttr('disabled');
            else
                $('input[name="expiration_date"]').attr('disabled', 'disabled');
        });
    });

    function showHideClientFields() {
        var group_id = $('select[name="group_id"]').val();
        if (group_id == 2) {
            $('.field_manager_id').show();
        }
        else {
            $('.field_manager_id').hide();
        }
    }

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