@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_ports">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation">
                    <a href="javascript:" type="button" data-modal="update_config" data-url="{{ route('admin.ports.do_update_config') }}">
                        <i class="icon restart" title="{{ trans('admin.update_config_and') }}"></i>
                    </a>
                </li>
                <li role="presentation">
                    <a href="javascript:" type="button" data-modal="update_config" data-url="{{ route('admin.ports.do_reset_default') }}">
                        <i class="icon reset" title="{{ trans('admin.reset_default') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{{ trans('admin.tracking_ports') }}</div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.Ports.table')
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $(document).on('click', '.extra-empty input', function() {
            var parent = $(this).closest('.extra-empty');
            var time = new Date().getTime();
            parent.removeClass('extra-empty');
            parent.after('<div class="row extra-empty"><div class="col-xs-6"><input class="form-control" name="extra[' + time + '][name]" type="text"></div><div class="col-xs-6"><div class="input-group"><input class="form-control" name="extra[' + time + '][value]" type="text"><span class="input-group-addon"><a href="javascript:" class="delete-extra-item remove-icon"><span aria-hidden="true">×</span></a></span></div></div></div>');
        });

        $(document).on('click', 'div.row:not(.extra-empty) .delete-extra-item', function() {
            $(this).closest('.row').remove();
        });

        tables.set_config('table_ports', {
            url:'{{ route("admin.ports.index") }}'
        });

        function ports_edit_modal_callback() {
            tables.get('table_ports');
        }
        function update_config_modal_callback() {
            tables.get('table_ports');
        }
    </script>
@stop