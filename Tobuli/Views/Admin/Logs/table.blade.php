<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeader('validation.attributes.name') !!}
            {!! tableHeader('global.date') !!}
            {!! tableHeader('admin.size') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @if (count($items))
            @foreach ($items as $date => $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item['name'] !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        {{ $item['name'] }}
                    </td>
                    <td>
                        {{ $date }}
                    </td>
                    <td>
                        {{ formatBytes($item['size']) }}
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('admin.logs.edit', $item['name']) }}">{{ trans('admin.download') }}</a></li>
                                <li><a href="{{ route('admin.logs.destroy') }}" class="js-confirm-link" data-confirm="{{ trans('admin.do_delete') }}" data-id="{{ $item['name'] }}" data-method="DELETE">{{ trans('global.delete') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5">
                    {{ trans('admin.no_data') }}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>