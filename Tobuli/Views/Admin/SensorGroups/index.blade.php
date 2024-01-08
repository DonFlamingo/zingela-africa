@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_sensor_groups">

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="sensor_groups_create" data-url="{{ route("admin.sensor_groups.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new_group') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{{ trans('admin.sensor_groups') }}</div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.SensorGroups.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_sensor_groups', {
        url:'{{ route("admin.sensor_groups.index") }}',
        delete_url:'{{ route("admin.sensor_groups.destroy") }}'
    });

    function sensor_groups_edit_modal_callback() {
        tables.get('table_sensor_groups');
    }

    function sensor_groups_create_modal_callback() {
        tables.get('table_sensor_groups');
    }

    function sensors_edit_modal_callback() {
        tables.get('table_sensor_group_sensors');
        tables.get('table_sensor_groups');
    }

    function sensors_create_modal_callback() {
        tables.get('table_sensor_group_sensors');
        tables.get('table_sensor_groups');
    }

    $(document).on('updateSensorGroupsTable', function () {
        tables.get('table_sensor_groups');
    });
</script>
@stop