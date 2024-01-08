@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_blocked_ips">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="blocked_ips_create" data-url="{{ route("admin.blocked_ips.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{{ trans('admin.blocked_ips') }}</div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.BlockedIps.table')
        </div>
    </div>
@stop

@section('javascript')
    <script>
        tables.set_config('table_blocked_ips', {
            url:'{{ route("admin.blocked_ips.index") }}'
        });

        function blocked_ips_create_modal_callback() {
            tables.get('table_blocked_ips');
        }
        function blocked_ips_destroy_modal_callback() {
            tables.get('table_blocked_ips');
        }
    </script>
@stop