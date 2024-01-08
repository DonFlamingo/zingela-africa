<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
            <tr>
                {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
                {!! tableHeader('validation.attributes.name') !!}
                {!! tableHeader('validation.attributes.type') !!}
                {!! tableHeader('validation.attributes.format') !!}
                {!! tableHeader('admin.size') !!}
                {!! tableHeader('validation.attributes.send_to_email') !!}
                {!! tableHeader('global.is_send') !!}
                <th></th>
            </tr>
        </thead>

        <tbody>
        @if (count($logs))
            @foreach ($logs as $log)
                <tr>
					<td>
                        <div class="checkbox">
                            <input type="checkbox" class="checkboxes" value="{{ $log->id }}">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ $log->title }}
                    </td>
                    <td>
                        {{ $log->type_text }}
                    </td>
					<td>
                        {{ $log->format_text }}
                    </td>
                    <td>
                        {{ formatBytes( $log->size ) }}
                    </td>
                    <td>
                        {{ $log->email }}
                    </td>
                    <td>
                        {{ $log->is_send ? trans('global.yes') : trans('global.no') }}
                    </td>
                    <td class="actions">
						<a href="{{ route('reports.log_download', $log->id) }}"><i class="icon download"></i></a>
                        <a href="{{ route('reports.log_destroy') }}" class="js-confirm-link remove-icon" data-confirm="{{ trans('admin.do_delete') }}" data-id="{{ $log->id }}" data-method="DELETE" alt="{{ trans('global.delete') }}">
                            <i class="icon delete"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" class="no-data">
                    {{ trans('admin.no_data') }}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $logs->setPath(route('reports.logs'))->render() !!}
</div>

<div class="modal" id="js-confirm-link" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                loading
            </div>
            <div class="modal-footer">
                <button type="button" value="cancel" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</button>
                <button type="button" value="confirm" class="btn btn-action submit js-confirm-link-yes">{{ trans('admin.confirm') }}</button>
            </div>
        </div>
    </div>
</div>