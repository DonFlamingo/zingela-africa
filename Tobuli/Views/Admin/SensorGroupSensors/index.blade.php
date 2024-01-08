@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('admin.manage_sensors') }}
@stop

@section('body')
    <div id ="table_sensor_group_sensors">
        <div class="action-block">
            <a href="javascript:" class="btn btn-action" data-modal="sensors_create" data-url="{{ route("admin.sensor_group_sensors.create", [$id]) }}" type="button">
                <i class="icon add"></i> {{ trans('front.add_new') }}
            </a>
        </div>
        <div data-table>
            @include('Admin.SensorGroupSensors.table')
        </div>
    </div>
@stop

@section('footer')
    <script>
        tables.set_config('table_sensor_group_sensors', {
            url:'{{ route("admin.sensor_group_sensors.index", [$id, '1']) }}',
            delete_url:'{{ route("admin.sensor_group_sensors.destroy") }}'
        });
    </script>
@stop