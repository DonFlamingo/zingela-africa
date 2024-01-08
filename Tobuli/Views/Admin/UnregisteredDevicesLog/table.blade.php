<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeader('validation.attributes.imei') !!}
            {!! tableHeader('validation.attributes.port') !!}
            <th>IP</th>
            {!! tableHeader('global.date') !!}
            {!! tableHeader('admin.tried_to_connect') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @if (count($items))
            @foreach ($items as $date => $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item->imei !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ $item->imei }}
                    </td>
                    <td>
                        {{ $item->port }}
                    </td>
                    <td>
                        {{ $item->ip }}
                    </td>
                    <td>
                        {{ datetime($item->date) }}
                    </td>
                    <td>
                        {{ $item->times }}
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('admin.unregistered_devices_log.destroy') }}" class="js-confirm-link" data-confirm="{{ trans('admin.do_delete') }}" data-id="{{ $item->date }}" data-method="DELETE">{{ trans('global.delete') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">
                    {{ trans('admin.no_data') }}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>